<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Supplier Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for all suppliers including their
    | field mappings, priority, and connection details.
    |
    */

    'suppliers' => [
        'ls' => [
            'name' => 'Supplier A (LS)',
            'enabled' => true,
            'priority' => 1, // Higher priority suppliers override lower priority ones
            'model' => \App\Models\SupplierLsProduct::class,
            'table' => 'supplier_ls_products',

            // Field mappings: 'supplier_field' => 'our_field'
            'mappings' => [
                // Identification & Classification
                'STOCK CODE' => 'supplier_code',
                'CATEGORY CODE' => 'category_code',
                'CATEGORY NAME' => 'category_name',
                'SUBCATEGORY NAME' => 'subcategory_name',
                'MANUFACTURER' => 'brand_name',
                'MANUFACTURER SKU' => 'brand_sku',

                // Product Description
                'SHORT DESCRIPTION' => 'name',
                'LONG DESCRIPTION' => 'description',
                'BAR CODE' => 'barcode',
                'IMAGE' => 'image_url',

                // Pricing
                'DBP' => 'cost_price',
                'RRP' => 'retail_price',

                // Physical Specifications
                'WEIGHT' => 'weight',
                'LENGTH' => 'length',
                'WIDTH' => 'width',
                'HEIGHT' => 'height',

                // Product Details
                'WARRANTY' => 'warranty',
                'ALTERNATIVE REPLACEMENTS' => 'alternative_skus',
                'OPTIONAL ACCESSORIES' => 'accessory_skus',

                // Stock Levels
                'AT' => 'stock_total',
                'AA' => 'stock_warehouse_a',
                'AQ' => 'stock_warehouse_b',
                'AN' => 'stock_warehouse_c',
                'AV' => 'stock_warehouse_d',
                'AW' => 'stock_warehouse_e',

                // ETA Dates
                'ETAA' => 'eta_warehouse_a',
                'ETAQ' => 'eta_warehouse_b',
                'ETAN' => 'eta_warehouse_c',
                'ETAV' => 'eta_warehouse_d',
                'ETAW' => 'eta_warehouse_e',
            ],
        ],

        'supplier1' => [
            'name' => 'Supplier 1',
            'enabled' => true,
            'priority' => 2,
            'model' => \App\Models\Supplier1Product::class,
            'table' => 'supplier1_products',

            // Field mappings for existing supplier1
            'mappings' => [
                'sku' => 'supplier_code',
                'name' => 'name',
                'shortdescription' => 'short_description',
                'longdescription' => 'description',
                'category1' => 'category_name',
                'costprice' => 'cost_price',
                'saleprice' => 'retail_price',
                'quantity' => 'stock_total',
                'length' => 'length',
                'width' => 'width',
                'height' => 'height',
                'weight' => 'weight',
                'imagesrc' => 'image_url',
                'asin' => 'asin',
                'ean' => 'barcode',
                'isbn' => 'isbn',
                'upc' => 'upc',
            ],
        ],

        'alloy' => [
            'name' => 'Alloy',
            'enabled' => true,
            'priority' => 3,
            'model' => \App\Models\AlloyProduct::class,
            'table' => 'alloy_products',

            // Field mappings: 'alloy_field' => 'our_field'
            'mappings' => [
                // Identification & Classification
                'PartNumber' => 'supplier_code',
                'SupplierPartNumber' => 'supplier_part_number',
                'CategoryName' => 'category_name',
                'Category' => 'category_code',
                'Group' => 'subcategory_name',
                'Manufacturer' => 'brand_name',
                'ManufacPrefix' => 'brand_prefix',

                // Product Description
                'Name' => 'name',
                'Description' => 'description',
                'HTMLDescription' => 'description_html',
                'FeaturesBenefits' => 'features',
                'MarketingComments' => 'marketing_description',
                'GeneralComments' => 'general_comments',
                'EAN' => 'barcode',

                // Pricing
                'PriceCostEx' => 'cost_price',
                'PriceRetailEx' => 'retail_price',
                'TaxType' => 'tax_type',
                'TaxRate' => 'tax_rate',

                // Physical Specifications
                'Weight' => 'weight',
                'Height' => 'height',
                'Width' => 'width',
                'Depth' => 'length',
                'Unit' => 'unit_of_measure',

                // Product Details
                'Warranty' => 'warranty',
                'ProductSpecificURL' => 'product_url',
                'image_thumbnail' => 'image_url',
                'PDF_Available' => 'pdf_available',

                // Stock Levels
                'Quantity' => 'stock_total',
                'Qty_ADL' => 'stock_warehouse_adl',
                'Qty_BNE' => 'stock_warehouse_bne',
                'Qty_MEL' => 'stock_warehouse_mel',
                'Qty_SYD' => 'stock_warehouse_syd',

                // ETA & Updates
                'ETADate' => 'eta_date',
                'ETAStatus' => 'eta_status',
                'StockRecordUpdated' => 'last_updated',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Merge Strategy
    |--------------------------------------------------------------------------
    |
    | Defines how data from multiple suppliers should be merged.
    | Options: 'priority' (use highest priority non-empty value),
    |          'newest' (use most recently updated value),
    |          'custom' (use custom merge logic)
    |
    */
    'merge_strategy' => 'priority',

    /*
    |--------------------------------------------------------------------------
    | Required Fields
    |--------------------------------------------------------------------------
    |
    | Fields that must be present for a product to be considered valid
    |
    */
    'required_fields' => [
        'supplier_code',
        'name',
    ],
];
