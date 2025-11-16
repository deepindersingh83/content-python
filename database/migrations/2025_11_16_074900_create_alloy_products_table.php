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
        Schema::create('alloy_products', function (Blueprint $table) {
            $table->id();

            // Identification & Classification
            $table->string('PartNumber')->index(); // Primary product identifier
            $table->string('SupplierPartNumber')->nullable();
            $table->string('CategoryName')->nullable();
            $table->string('Category')->nullable();
            $table->string('Group')->nullable(); // Subcategory/Group
            $table->string('Manufacturer')->nullable();
            $table->string('ManufacPrefix')->nullable();

            // Product Description
            $table->string('Name');
            $table->text('Description')->nullable();
            $table->text('HTMLDescription')->nullable();
            $table->text('FeaturesBenefits')->nullable();
            $table->text('MarketingComments')->nullable();
            $table->text('GeneralComments')->nullable();
            $table->string('EAN')->nullable();

            // Pricing
            $table->decimal('PriceCostEx', 10, 2)->nullable();
            $table->decimal('PriceRetailEx', 10, 2)->nullable();
            $table->string('TaxType')->nullable();
            $table->decimal('TaxRate', 5, 2)->nullable();

            // Physical Specifications
            $table->decimal('Weight', 10, 3)->nullable();
            $table->decimal('Height', 10, 2)->nullable();
            $table->decimal('Width', 10, 2)->nullable();
            $table->decimal('Depth', 10, 2)->nullable();
            $table->string('Unit')->nullable(); // Unit of measure

            // Product Details
            $table->string('Warranty')->nullable();
            $table->text('ProductSpecificURL')->nullable();
            $table->text('image_thumbnail')->nullable();
            $table->boolean('PDF_Available')->default(false);

            // Stock Levels (Multiple warehouse locations)
            $table->integer('Quantity')->default(0); // Total quantity
            $table->integer('Qty_ADL')->default(0); // Adelaide
            $table->integer('Qty_BNE')->default(0); // Brisbane
            $table->integer('Qty_MEL')->default(0); // Melbourne
            $table->integer('Qty_SYD')->default(0); // Sydney

            // ETA & Updates
            $table->date('ETADate')->nullable();
            $table->string('ETAStatus')->nullable();
            $table->timestamp('StockRecordUpdated')->nullable();

            $table->timestamps();

            // Indexes for common lookups
            $table->index('EAN');
            $table->index('SupplierPartNumber');
            $table->index(['Category', 'Group']);
            $table->index('Manufacturer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alloy_products');
    }
};
