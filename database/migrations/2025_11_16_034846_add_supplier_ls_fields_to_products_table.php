<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Additional identification fields
            $table->string('supplier_code')->nullable()->after('upc');
            $table->string('category_code')->nullable()->after('category4');
            $table->string('subcategory_name')->nullable()->after('category_code');
            $table->string('brand_name')->nullable()->after('subcategory_name');
            $table->string('brand_sku')->nullable()->after('brand_name');
            $table->string('barcode')->nullable()->after('brand_sku');

            // Rename/add description fields
            $table->text('description')->nullable()->after('longdescription');
            $table->decimal('retail_price', 10, 2)->nullable()->after('saleprice');

            // Product details
            $table->string('warranty')->nullable()->after('imagesrc');
            $table->text('alternative_skus')->nullable()->after('warranty');
            $table->text('accessory_skus')->nullable()->after('alternative_skus');

            // Warehouse stock levels
            $table->integer('stock_total')->default(0)->after('quantity');
            $table->integer('stock_warehouse_a')->default(0)->after('stock_total');
            $table->integer('stock_warehouse_b')->default(0)->after('stock_warehouse_a');
            $table->integer('stock_warehouse_c')->default(0)->after('stock_warehouse_b');
            $table->integer('stock_warehouse_d')->default(0)->after('stock_warehouse_c');
            $table->integer('stock_warehouse_e')->default(0)->after('stock_warehouse_d');

            // ETA dates
            $table->date('eta_warehouse_a')->nullable()->after('stock_warehouse_e');
            $table->date('eta_warehouse_b')->nullable()->after('eta_warehouse_a');
            $table->date('eta_warehouse_c')->nullable()->after('eta_warehouse_b');
            $table->date('eta_warehouse_d')->nullable()->after('eta_warehouse_c');
            $table->date('eta_warehouse_e')->nullable()->after('eta_warehouse_d');

            // Add indexes
            $table->index('supplier_code');
            $table->index('barcode');
            $table->index('brand_sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['supplier_code']);
            $table->dropIndex(['barcode']);
            $table->dropIndex(['brand_sku']);

            $table->dropColumn([
                'supplier_code',
                'category_code',
                'subcategory_name',
                'brand_name',
                'brand_sku',
                'barcode',
                'description',
                'retail_price',
                'warranty',
                'alternative_skus',
                'accessory_skus',
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
            ]);
        });
    }
};
