<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductSyncService
{
    protected SupplierMappingService $mappingService;

    public function __construct(SupplierMappingService $mappingService)
    {
        $this->mappingService = $mappingService;
    }

    /**
     * Sync products from all suppliers into the master products table
     */
    public function syncAllProducts()
    {
        DB::beginTransaction();

        try {
            // Get all enabled suppliers
            $suppliers = $this->mappingService->getEnabledSuppliers();

            if (empty($suppliers)) {
                throw new \Exception('No enabled suppliers found in configuration');
            }

            // Load all products from all suppliers
            $allSupplierProducts = $this->loadAllSupplierProducts($suppliers);

            // Group products by unique identifier across all suppliers
            $productGroups = $this->groupProductsAcrossSuppliers($allSupplierProducts, $suppliers);

            // Merge and sync each product group
            $syncedCount = 0;
            foreach ($productGroups as $uniqueId => $productsFromSuppliers) {
                if ($this->mergeAndSyncProduct($productsFromSuppliers, $suppliers)) {
                    $syncedCount++;
                }
            }

            DB::commit();

            return [
                'success' => true,
                'message' => "Products synced successfully. {$syncedCount} products processed.",
                'count' => $syncedCount
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product sync failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Error syncing products: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Load all products from all enabled suppliers
     *
     * @param array $suppliers
     * @return array
     */
    protected function loadAllSupplierProducts(array $suppliers): array
    {
        $allProducts = [];

        foreach ($suppliers as $supplierKey => $config) {
            $modelClass = $config['model'] ?? null;

            if (!$modelClass || !class_exists($modelClass)) {
                Log::warning("Model class not found for supplier: {$supplierKey}");
                continue;
            }

            try {
                $products = $modelClass::all();
                $allProducts[$supplierKey] = $products;

                Log::info("Loaded {$products->count()} products from supplier: {$supplierKey}");
            } catch (\Exception $e) {
                Log::error("Failed to load products from supplier {$supplierKey}: " . $e->getMessage());
                $allProducts[$supplierKey] = collect([]);
            }
        }

        return $allProducts;
    }

    /**
     * Group products by unique identifier across all suppliers
     *
     * @param array $allSupplierProducts
     * @param array $suppliers
     * @return array
     */
    protected function groupProductsAcrossSuppliers(array $allSupplierProducts, array $suppliers): array
    {
        $productGroups = [];

        foreach ($allSupplierProducts as $supplierKey => $products) {
            foreach ($products as $product) {
                // Convert model to array for mapping
                $productArray = $product->toArray();

                // Map to internal format
                $mappedData = $this->mappingService->mapToInternal($supplierKey, $productArray);

                // Get unique identifier
                $uniqueId = $this->mappingService->getUniqueIdentifier($mappedData);

                if (!$uniqueId) {
                    Log::warning("Product from {$supplierKey} has no unique identifier", [
                        'product' => $mappedData
                    ]);
                    continue;
                }

                // Group by unique identifier
                if (!isset($productGroups[$uniqueId])) {
                    $productGroups[$uniqueId] = [];
                }

                $productGroups[$uniqueId][$supplierKey] = $mappedData;
            }
        }

        return $productGroups;
    }

    /**
     * Merge and sync a single product from multiple suppliers
     *
     * @param array $productsFromSuppliers
     * @param array $suppliers
     * @return bool
     */
    protected function mergeAndSyncProduct(array $productsFromSuppliers, array $suppliers): bool
    {
        try {
            // Merge data from all suppliers based on priority
            $mergedData = $this->mappingService->mergeSupplierData($productsFromSuppliers);

            // Validate required fields
            if (!$this->mappingService->validateRequiredFields($mergedData)) {
                Log::warning('Product missing required fields', ['data' => $mergedData]);
                return false;
            }

            // Determine the unique identifier for database lookup
            $uniqueId = $this->mappingService->getUniqueIdentifier($mergedData);

            if (!$uniqueId) {
                Log::warning('Cannot determine unique identifier for product', ['data' => $mergedData]);
                return false;
            }

            // Update or create the product
            $product = Product::updateOrCreate(
                [
                    'supplier_code' => $mergedData['supplier_code'] ?? null,
                ],
                $mergedData
            );

            Log::info("Product synced successfully", [
                'id' => $product->id,
                'supplier_code' => $product->supplier_code,
                'name' => $product->name
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to merge and sync product', [
                'error' => $e->getMessage(),
                'data' => $productsFromSuppliers
            ]);
            return false;
        }
    }

    /**
     * Sync products from a specific supplier
     *
     * @param string $supplierKey
     * @return array
     */
    public function syncSupplier(string $supplierKey)
    {
        DB::beginTransaction();

        try {
            $config = $this->mappingService->getSupplierConfig($supplierKey);

            if (!$config) {
                throw new \Exception("Supplier '{$supplierKey}' not found");
            }

            if (!($config['enabled'] ?? false)) {
                throw new \Exception("Supplier '{$supplierKey}' is not enabled");
            }

            $modelClass = $config['model'] ?? null;

            if (!$modelClass || !class_exists($modelClass)) {
                throw new \Exception("Model class not found for supplier: {$supplierKey}");
            }

            $products = $modelClass::all();
            $syncedCount = 0;

            foreach ($products as $product) {
                $productArray = $product->toArray();
                $mappedData = $this->mappingService->mapToInternal($supplierKey, $productArray);

                if (!$this->mappingService->validateRequiredFields($mappedData)) {
                    continue;
                }

                $uniqueId = $this->mappingService->getUniqueIdentifier($mappedData);

                if ($uniqueId) {
                    Product::updateOrCreate(
                        ['supplier_code' => $mappedData['supplier_code'] ?? null],
                        $mappedData
                    );
                    $syncedCount++;
                }
            }

            DB::commit();

            return [
                'success' => true,
                'message' => "Synced {$syncedCount} products from supplier: {$supplierKey}",
                'count' => $syncedCount
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Error syncing supplier: ' . $e->getMessage()
            ];
        }
    }
}
