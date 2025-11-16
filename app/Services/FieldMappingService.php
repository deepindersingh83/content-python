<?php

namespace App\Services;

class FieldMappingService
{
    /**
     * Alloy (Supplier 2) field mappings
     * Maps Alloy supplier CSV column names to standard product database fields
     */
    public const ALLOY_FIELD_MAP = [
        // Identifiers
        'PartNumber' => 'sku',
        'EAN' => 'ean',
        'SupplierPartNumber' => 'upc',

        // Product Information
        'Name' => 'name',
        'Description' => 'shortdescription',
        'HTMLDescription' => 'longdescription',

        // Categories
        'Category' => 'category1',
        'CategoryName' => 'category2',
        'Group' => 'category3',

        // Pricing
        'PriceCostEx' => 'costprice',
        'PriceRetailEx' => 'saleprice',

        // Inventory
        'Quantity' => 'quantity',

        // Dimensions
        'Width' => 'width',
        'Height' => 'height',
        'Depth' => 'length',  // Depth maps to length
        'Weight' => 'weight',

        // Media
        'image_thumbnail' => 'imagesrc',
    ];

    /**
     * Alloy warehouse quantity fields
     * These will be summed to calculate total quantity
     */
    public const ALLOY_WAREHOUSE_FIELDS = [
        'Qty_ADL',
        'Qty_BNE',
        'Qty_MEL',
        'Qty_SYD',
    ];

    /**
     * Additional Alloy fields that don't map to standard fields
     * but should be stored in supplier2_products table
     */
    public const ALLOY_EXTENDED_FIELDS = [
        'ManufacPrefix',
        'Manufacturer',
        'Unit',
        'TaxType',
        'TaxRate',
        'FeaturesBenefits',
        'MarketingComments',
        'GeneralComments',
        'ProductSpecificURL',
        'Warranty',
        'PDF_Available',
        'StockRecordUpdated',
        'ETADate',
        'ETAStatus',
        'Qty_ADL',
        'Qty_BNE',
        'Qty_MEL',
        'Qty_SYD',
    ];

    /**
     * Map Alloy CSV row to standard product fields
     *
     * @param array $alloyData Associative array of Alloy CSV data
     * @return array Mapped data with standard field names
     */
    public function mapAlloyToStandard(array $alloyData): array
    {
        $mapped = [];

        // Map standard fields
        foreach (self::ALLOY_FIELD_MAP as $alloyField => $standardField) {
            if (isset($alloyData[$alloyField]) && $alloyData[$alloyField] !== '') {
                $mapped[$standardField] = $alloyData[$alloyField];
            }
        }

        // Calculate total quantity from warehouse quantities if not already set
        if (!isset($mapped['quantity']) || $mapped['quantity'] === '') {
            $totalQty = 0;
            foreach (self::ALLOY_WAREHOUSE_FIELDS as $warehouseField) {
                if (isset($alloyData[$warehouseField]) && is_numeric($alloyData[$warehouseField])) {
                    $totalQty += (int) $alloyData[$warehouseField];
                }
            }
            if ($totalQty > 0) {
                $mapped['quantity'] = $totalQty;
            }
        }

        return $mapped;
    }

    /**
     * Get all Alloy fields (both mapped and extended)
     *
     * @param array $alloyData Associative array of Alloy CSV data
     * @return array Complete Alloy data with both standard and extended fields
     */
    public function getAlloyFields(array $alloyData): array
    {
        $result = [];

        // Get mapped standard fields
        $standardFields = $this->mapAlloyToStandard($alloyData);

        // Add all Alloy source fields for reference
        foreach ($alloyData as $key => $value) {
            if ($value !== '') {
                $result[$key] = $value;
            }
        }

        return array_merge($result, $standardFields);
    }

    /**
     * Validate required Alloy fields
     *
     * @param array $alloyData
     * @return bool
     */
    public function validateAlloyData(array $alloyData): bool
    {
        // At minimum, we need PartNumber or EAN to identify the product
        return isset($alloyData['PartNumber']) || isset($alloyData['EAN']);
    }

    /**
     * Get reverse mapping (standard field to Alloy field)
     *
     * @return array
     */
    public function getStandardToAlloyMap(): array
    {
        return array_flip(self::ALLOY_FIELD_MAP);
    }
}
