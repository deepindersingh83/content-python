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
        Schema::create('supplier_ls_products', function (Blueprint $table) {
            $table->id();

            // Identification & Classification
            $table->string('supplier_code')->index(); // STOCK CODE
            $table->string('category_code')->nullable();
            $table->string('category_name')->nullable();
            $table->string('subcategory_name')->nullable();
            $table->string('brand_name')->nullable(); // MANUFACTURER
            $table->string('brand_sku')->nullable(); // MANUFACTURER SKU

            // Product Description
            $table->string('name'); // SHORT DESCRIPTION
            $table->text('description')->nullable(); // LONG DESCRIPTION
            $table->string('barcode')->nullable(); // BAR CODE
            $table->text('image_url')->nullable(); // IMAGE

            // Pricing
            $table->decimal('cost_price', 10, 2)->nullable(); // DBP
            $table->decimal('retail_price', 10, 2)->nullable(); // RRP

            // Physical Specifications
            $table->decimal('weight', 10, 2)->nullable();
            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();

            // Product Details
            $table->string('warranty')->nullable();
            $table->text('alternative_skus')->nullable(); // ALTERNATIVE REPLACEMENTS (JSON or comma-separated)
            $table->text('accessory_skus')->nullable(); // OPTIONAL ACCESSORIES (JSON or comma-separated)

            // Stock Levels
            $table->integer('stock_total')->default(0); // AT
            $table->integer('stock_warehouse_a')->default(0); // AA
            $table->integer('stock_warehouse_b')->default(0); // AQ
            $table->integer('stock_warehouse_c')->default(0); // AN
            $table->integer('stock_warehouse_d')->default(0); // AV
            $table->integer('stock_warehouse_e')->default(0); // AW

            // ETA (Expected Time of Arrival) Dates
            $table->date('eta_warehouse_a')->nullable(); // ETAA
            $table->date('eta_warehouse_b')->nullable(); // ETAQ
            $table->date('eta_warehouse_c')->nullable(); // ETAN
            $table->date('eta_warehouse_d')->nullable(); // ETAV
            $table->date('eta_warehouse_e')->nullable(); // ETAW

            $table->timestamps();

            // Indexes for common lookups
            $table->index('barcode');
            $table->index('brand_sku');
            $table->index(['category_code', 'subcategory_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_ls_products');
    }
};
