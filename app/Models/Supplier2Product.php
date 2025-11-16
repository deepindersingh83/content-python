<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier2Product extends Model
{
    protected $table = 'supplier2_products';

    protected $fillable = [
        // Standard product fields
        'sku',
        'asin',
        'ean',
        'isbn',
        'upc',
        'name',
        'shortdescription',
        'longdescription',
        'category1',
        'category2',
        'category3',
        'category4',
        'costprice',
        'saleprice',
        'quantity',
        'length',
        'width',
        'height',
        'weight',
        'imagesrc',

        // Alloy-specific fields
        'manufac_prefix',
        'manufacturer',
        'unit',
        'tax_type',
        'tax_rate',
        'features_benefits',
        'marketing_comments',
        'general_comments',
        'product_specific_url',
        'warranty',
        'pdf_available',
        'stock_record_updated',
        'eta_date',
        'eta_status',
        'qty_adl',
        'qty_bne',
        'qty_mel',
        'qty_syd',
    ];
}
