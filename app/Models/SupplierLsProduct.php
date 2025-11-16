<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierLsProduct extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'supplier_ls_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Identification & Classification
        'supplier_code',
        'category_code',
        'category_name',
        'subcategory_name',
        'brand_name',
        'brand_sku',

        // Product Description
        'name',
        'description',
        'barcode',
        'image_url',

        // Pricing
        'cost_price',
        'retail_price',

        // Physical Specifications
        'weight',
        'length',
        'width',
        'height',

        // Product Details
        'warranty',
        'alternative_skus',
        'accessory_skus',

        // Stock Levels
        'stock_total',
        'stock_warehouse_a',
        'stock_warehouse_b',
        'stock_warehouse_c',
        'stock_warehouse_d',
        'stock_warehouse_e',

        // ETA Dates
        'eta_warehouse_a',
        'eta_warehouse_b',
        'eta_warehouse_c',
        'eta_warehouse_d',
        'eta_warehouse_e',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cost_price' => 'decimal:2',
        'retail_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
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

    /**
     * Get alternative SKUs as an array
     *
     * @return array
     */
    public function getAlternativeSkusArrayAttribute(): array
    {
        if (empty($this->alternative_skus)) {
            return [];
        }

        // Try to decode as JSON first
        $decoded = json_decode($this->alternative_skus, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // Fallback to comma-separated
        return array_filter(array_map('trim', explode(',', $this->alternative_skus)));
    }

    /**
     * Get accessory SKUs as an array
     *
     * @return array
     */
    public function getAccessorySkusArrayAttribute(): array
    {
        if (empty($this->accessory_skus)) {
            return [];
        }

        // Try to decode as JSON first
        $decoded = json_decode($this->accessory_skus, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // Fallback to comma-separated
        return array_filter(array_map('trim', explode(',', $this->accessory_skus)));
    }

    /**
     * Get total stock across all warehouses
     * Useful if AT (stock_total) is not provided
     *
     * @return int
     */
    public function getCalculatedStockTotalAttribute(): int
    {
        return $this->stock_warehouse_a +
               $this->stock_warehouse_b +
               $this->stock_warehouse_c +
               $this->stock_warehouse_d +
               $this->stock_warehouse_e;
    }
}
