<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlloyProduct extends Model
{
    protected $table = 'alloy_products';

    protected $fillable = [
        // Identification & Classification
        'PartNumber',
        'SupplierPartNumber',
        'CategoryName',
        'Category',
        'Group',
        'Manufacturer',
        'ManufacPrefix',

        // Product Description
        'Name',
        'Description',
        'HTMLDescription',
        'FeaturesBenefits',
        'MarketingComments',
        'GeneralComments',
        'EAN',

        // Pricing
        'PriceCostEx',
        'PriceRetailEx',
        'TaxType',
        'TaxRate',

        // Physical Specifications
        'Weight',
        'Height',
        'Width',
        'Depth',
        'Unit',

        // Product Details
        'Warranty',
        'ProductSpecificURL',
        'image_thumbnail',
        'PDF_Available',

        // Stock Levels
        'Quantity',
        'Qty_ADL',
        'Qty_BNE',
        'Qty_MEL',
        'Qty_SYD',

        // ETA & Updates
        'ETADate',
        'ETAStatus',
        'StockRecordUpdated',
    ];

    protected $casts = [
        'PriceCostEx' => 'decimal:2',
        'PriceRetailEx' => 'decimal:2',
        'TaxRate' => 'decimal:2',
        'Weight' => 'decimal:3',
        'Height' => 'decimal:2',
        'Width' => 'decimal:2',
        'Depth' => 'decimal:2',
        'Quantity' => 'integer',
        'Qty_ADL' => 'integer',
        'Qty_BNE' => 'integer',
        'Qty_MEL' => 'integer',
        'Qty_SYD' => 'integer',
        'PDF_Available' => 'boolean',
        'StockRecordUpdated' => 'datetime',
        'ETADate' => 'date',
    ];
}
