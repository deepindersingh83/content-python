<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Supplier1Product;
use App\Models\Supplier2Product;
use Illuminate\Support\Facades\DB;

class ProductSyncService
{
    /**
     * Sync products from all suppliers into the master products table
     */
    public function syncAllProducts()
    {
        DB::beginTransaction();

        try {
            // Get all unique product identifiers (SKU) from all suppliers
            $supplier1Products = Supplier1Product::all();
            $supplier2Products = Supplier2Product::all();

            // Group supplier2 products by SKU for easy lookup
            $supplier2ByKey = $this->groupByUniqueKey($supplier2Products);

            // Process supplier1 products (primary supplier)
            foreach ($supplier1Products as $supplier1Product) {
                $key = $this->getUniqueKey($supplier1Product);
                $supplier2Product = $supplier2ByKey[$key] ?? null;

                $this->mergeProduct($supplier1Product, $supplier2Product);
            }

            // Process supplier2 products that don't exist in supplier1
            $supplier1ByKey = $this->groupByUniqueKey($supplier1Products);
            foreach ($supplier2Products as $supplier2Product) {
                $key = $this->getUniqueKey($supplier2Product);

                // Only process if this product doesn't exist in supplier1
                if (!isset($supplier1ByKey[$key])) {
                    $this->mergeProduct(null, $supplier2Product);
                }
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Products synced successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Error syncing products: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Merge product data from suppliers with fallback logic
     */
    protected function mergeProduct($supplier1Product, $supplier2Product)
    {
        $productData = [];

        // Define all fields to sync
        $fields = [
            'sku', 'asin', 'ean', 'isbn', 'upc', 'name',
            'shortdescription', 'longdescription',
            'category1', 'category2', 'category3', 'category4',
            'costprice', 'saleprice', 'quantity',
            'length', 'width', 'height', 'weight', 'imagesrc'
        ];

        // Merge logic: Use supplier1 value if not empty, else use supplier2 value
        foreach ($fields as $field) {
            $supplier1Value = $supplier1Product ? $supplier1Product->$field : null;
            $supplier2Value = $supplier2Product ? $supplier2Product->$field : null;

            // Use supplier1 value if it's not empty/null, otherwise fallback to supplier2
            $productData[$field] = !empty($supplier1Value) ? $supplier1Value : $supplier2Value;
        }

        // Get the unique key for this product (using SKU as primary identifier)
        $uniqueKey = $productData['sku'];

        if (empty($uniqueKey)) {
            // Try other identifiers if SKU is empty
            $uniqueKey = $productData['asin'] ?? $productData['ean'] ?? $productData['isbn'] ?? $productData['upc'] ?? null;
        }

        if ($uniqueKey) {
            // Update or create the product in the master table
            Product::updateOrCreate(
                ['sku' => $productData['sku']], // Match by SKU
                $productData
            );
        }
    }

    /**
     * Get unique key for a product (using SKU as primary)
     */
    protected function getUniqueKey($product)
    {
        if (!$product) return null;

        return $product->sku ?? $product->asin ?? $product->ean ?? $product->isbn ?? $product->upc ?? null;
    }

    /**
     * Group products by their unique key
     */
    protected function groupByUniqueKey($products)
    {
        $grouped = [];

        foreach ($products as $product) {
            $key = $this->getUniqueKey($product);
            if ($key) {
                $grouped[$key] = $product;
            }
        }

        return $grouped;
    }
}
