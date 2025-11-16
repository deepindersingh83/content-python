<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'sku',
        'asin',
        'ean',
        'isbn',
        'upc',
        'supplier_code',
        'name',
        'shortdescription',
        'longdescription',
        'description',
        'category1',
        'category2',
        'category3',
        'category4',
        'category_code',
        'subcategory_name',
        'brand_name',
        'brand_sku',
        'barcode',
        'costprice',
        'saleprice',
        'retail_price',
        'quantity',
        'stock_total',
        'stock_warehouse_a',
        'stock_warehouse_b',
        'stock_warehouse_c',
        'stock_warehouse_d',
        'stock_warehouse_e',
        'eta_warehouse_a',
        'eta_warehouse_b',
        'eta_warehouse_c',
        'eta_warehouse_d',
        'eta_warehouse_e',
        'length',
        'width',
        'height',
        'weight',
        'imagesrc',
        'warranty',
        'alternative_skus',
        'accessory_skus',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'costprice' => 'decimal:2',
        'saleprice' => 'decimal:2',
        'retail_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'quantity' => 'integer',
        'stock_total' => 'integer',
        'stock_warehouse_a' => 'integer',
        'stock_warehouse_b' => 'integer',
        'stock_warehouse_c' => 'integer',
        'stock_warehouse_d' => 'integer',
        'stock_warehouse_e' => 'integer',
        'eta_warehouse_a' => 'date',
        'eta_warehouse_b' => 'date',
        'eta_warehouse_c' => 'date',
        'eta_warehouse_d' => 'date',
        'eta_warehouse_e' => 'date',
    ];
}
