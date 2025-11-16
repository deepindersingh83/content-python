<?php

namespace App\Services;

use App\Models\Supplier2Product;
use Illuminate\Support\Facades\DB;

class AlloyImportService
{
    protected $fieldMappingService;

    public function __construct()
    {
        $this->fieldMappingService = new FieldMappingService();
    }

    /**
     * Import Alloy CSV file into supplier2_products table
     *
     * @param string $filePath Path to CSV file
     * @return array Result with success status and statistics
     */
    public function importCsv(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [
                'success' => false,
                'message' => 'CSV file not found: ' . $filePath,
            ];
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return [
                'success' => false,
                'message' => 'Unable to open CSV file',
            ];
        }

        DB::beginTransaction();

        try {
            $headers = fgetcsv($handle);
            if (!$headers) {
                throw new \Exception('CSV file is empty or has invalid format');
            }

            $imported = 0;
            $updated = 0;
            $skipped = 0;
            $errors = [];

            while (($row = fgetcsv($handle)) !== false) {
                try {
                    // Combine headers with row data
                    $alloyData = array_combine($headers, $row);

                    // Validate data
                    if (!$this->fieldMappingService->validateAlloyData($alloyData)) {
                        $skipped++;
                        $errors[] = "Skipped row: Missing required fields (PartNumber or EAN)";
                        continue;
                    }

                    // Map Alloy fields to standard fields
                    $mappedData = $this->mapAlloyRow($alloyData);

                    // Find existing product by SKU or EAN
                    $existingProduct = Supplier2Product::where('sku', $mappedData['sku'])
                        ->orWhere('ean', $mappedData['ean'])
                        ->first();

                    if ($existingProduct) {
                        $existingProduct->update($mappedData);
                        $updated++;
                    } else {
                        Supplier2Product::create($mappedData);
                        $imported++;
                    }
                } catch (\Exception $e) {
                    $skipped++;
                    $errors[] = "Error processing row: " . $e->getMessage();
                }
            }

            fclose($handle);
            DB::commit();

            return [
                'success' => true,
                'message' => 'Import completed successfully',
                'statistics' => [
                    'imported' => $imported,
                    'updated' => $updated,
                    'skipped' => $skipped,
                    'total_rows' => $imported + $updated + $skipped,
                ],
                'errors' => $errors,
            ];
        } catch (\Exception $e) {
            fclose($handle);
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Map a single Alloy CSV row to supplier2_products table structure
     *
     * @param array $alloyData
     * @return array
     */
    protected function mapAlloyRow(array $alloyData): array
    {
        $mapped = [];

        // Map standard fields using FieldMappingService
        $standardFields = $this->fieldMappingService->mapAlloyToStandard($alloyData);
        $mapped = array_merge($mapped, $standardFields);

        // Map Alloy-specific extended fields
        $extendedFieldMap = [
            'ManufacPrefix' => 'manufac_prefix',
            'Manufacturer' => 'manufacturer',
            'Unit' => 'unit',
            'TaxType' => 'tax_type',
            'TaxRate' => 'tax_rate',
            'FeaturesBenefits' => 'features_benefits',
            'MarketingComments' => 'marketing_comments',
            'GeneralComments' => 'general_comments',
            'ProductSpecificURL' => 'product_specific_url',
            'Warranty' => 'warranty',
            'PDF_Available' => 'pdf_available',
            'StockRecordUpdated' => 'stock_record_updated',
            'ETADate' => 'eta_date',
            'ETAStatus' => 'eta_status',
            'Qty_ADL' => 'qty_adl',
            'Qty_BNE' => 'qty_bne',
            'Qty_MEL' => 'qty_mel',
            'Qty_SYD' => 'qty_syd',
        ];

        foreach ($extendedFieldMap as $alloyField => $dbField) {
            if (isset($alloyData[$alloyField]) && $alloyData[$alloyField] !== '') {
                $value = $alloyData[$alloyField];

                // Handle specific data type conversions
                switch ($dbField) {
                    case 'pdf_available':
                        $mapped[$dbField] = in_array(strtolower($value), ['yes', 'true', '1', 'y']);
                        break;
                    case 'tax_rate':
                        $mapped[$dbField] = is_numeric($value) ? (float) $value : null;
                        break;
                    case 'qty_adl':
                    case 'qty_bne':
                    case 'qty_mel':
                    case 'qty_syd':
                        $mapped[$dbField] = is_numeric($value) ? (int) $value : 0;
                        break;
                    case 'stock_record_updated':
                        // Try to parse date
                        try {
                            $mapped[$dbField] = $value ? date('Y-m-d H:i:s', strtotime($value)) : null;
                        } catch (\Exception $e) {
                            $mapped[$dbField] = null;
                        }
                        break;
                    case 'eta_date':
                        // Try to parse date
                        try {
                            $mapped[$dbField] = $value ? date('Y-m-d', strtotime($value)) : null;
                        } catch (\Exception $e) {
                            $mapped[$dbField] = null;
                        }
                        break;
                    default:
                        $mapped[$dbField] = $value;
                }
            }
        }

        return $mapped;
    }

    /**
     * Import from array (useful for API imports)
     *
     * @param array $alloyProducts Array of Alloy product data
     * @return array
     */
    public function importFromArray(array $alloyProducts): array
    {
        DB::beginTransaction();

        try {
            $imported = 0;
            $updated = 0;
            $skipped = 0;
            $errors = [];

            foreach ($alloyProducts as $alloyData) {
                try {
                    // Validate data
                    if (!$this->fieldMappingService->validateAlloyData($alloyData)) {
                        $skipped++;
                        $errors[] = "Skipped product: Missing required fields";
                        continue;
                    }

                    // Map Alloy fields to standard fields
                    $mappedData = $this->mapAlloyRow($alloyData);

                    // Find existing product by SKU or EAN
                    $existingProduct = Supplier2Product::where('sku', $mappedData['sku'])
                        ->orWhere('ean', $mappedData['ean'])
                        ->first();

                    if ($existingProduct) {
                        $existingProduct->update($mappedData);
                        $updated++;
                    } else {
                        Supplier2Product::create($mappedData);
                        $imported++;
                    }
                } catch (\Exception $e) {
                    $skipped++;
                    $errors[] = "Error processing product: " . $e->getMessage();
                }
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Import completed successfully',
                'statistics' => [
                    'imported' => $imported,
                    'updated' => $updated,
                    'skipped' => $skipped,
                    'total_rows' => $imported + $updated + $skipped,
                ],
                'errors' => $errors,
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
            ];
        }
    }
}
