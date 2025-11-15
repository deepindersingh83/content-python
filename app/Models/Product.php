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
    ];
}
