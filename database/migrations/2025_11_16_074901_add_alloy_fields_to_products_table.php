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
            $table->string('supplier_part_number')->nullable()->after('supplier_code');
            $table->string('brand_prefix')->nullable()->after('brand_name');

            // Extended description fields
            $table->text('description_html')->nullable()->after('description');
            $table->text('features')->nullable()->after('description_html');
            $table->text('marketing_description')->nullable()->after('features');
            $table->text('general_comments')->nullable()->after('marketing_description');

            // Tax information
            $table->string('tax_type')->nullable()->after('retail_price');
            $table->decimal('tax_rate', 5, 2)->nullable()->after('tax_type');

            // Additional product details
            $table->string('unit_of_measure')->nullable()->after('weight');
            $table->text('product_url')->nullable()->after('accessory_skus');
            $table->boolean('pdf_available')->default(false)->after('product_url');

            // Australian warehouse stock levels (Alloy-specific)
            $table->integer('stock_warehouse_adl')->default(0)->after('stock_warehouse_e');
            $table->integer('stock_warehouse_bne')->default(0)->after('stock_warehouse_adl');
            $table->integer('stock_warehouse_mel')->default(0)->after('stock_warehouse_bne');
            $table->integer('stock_warehouse_syd')->default(0)->after('stock_warehouse_mel');

            // ETA information (consolidated from Alloy)
            $table->date('eta_date')->nullable()->after('eta_warehouse_e');
            $table->string('eta_status')->nullable()->after('eta_date');
            $table->timestamp('last_updated')->nullable()->after('eta_status');

            // Add indexes
            $table->index('supplier_part_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['supplier_part_number']);

            $table->dropColumn([
                'supplier_part_number',
                'brand_prefix',
                'description_html',
                'features',
                'marketing_description',
                'general_comments',
                'tax_type',
                'tax_rate',
                'unit_of_measure',
                'product_url',
                'pdf_available',
                'stock_warehouse_adl',
                'stock_warehouse_bne',
                'stock_warehouse_mel',
                'stock_warehouse_syd',
                'eta_date',
                'eta_status',
                'last_updated',
            ]);
        });
    }
};
