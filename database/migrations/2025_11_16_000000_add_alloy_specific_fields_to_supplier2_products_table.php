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
        Schema::table('supplier2_products', function (Blueprint $table) {
            // Manufacturer Information
            $table->string('manufac_prefix')->nullable()->after('imagesrc');
            $table->string('manufacturer')->nullable()->after('manufac_prefix');

            // Unit & Tax Information
            $table->string('unit')->nullable()->after('manufacturer');
            $table->string('tax_type')->nullable()->after('unit');
            $table->decimal('tax_rate', 5, 2)->nullable()->after('tax_type');

            // Additional Product Information
            $table->text('features_benefits')->nullable()->after('tax_rate');
            $table->text('marketing_comments')->nullable()->after('features_benefits');
            $table->text('general_comments')->nullable()->after('marketing_comments');
            $table->string('product_specific_url')->nullable()->after('general_comments');
            $table->string('warranty')->nullable()->after('product_specific_url');

            // Document & Media
            $table->boolean('pdf_available')->default(false)->after('warranty');

            // Stock Information
            $table->timestamp('stock_record_updated')->nullable()->after('pdf_available');
            $table->date('eta_date')->nullable()->after('stock_record_updated');
            $table->string('eta_status')->nullable()->after('eta_date');

            // Warehouse Quantities
            $table->integer('qty_adl')->default(0)->after('eta_status')->comment('Adelaide warehouse quantity');
            $table->integer('qty_bne')->default(0)->after('qty_adl')->comment('Brisbane warehouse quantity');
            $table->integer('qty_mel')->default(0)->after('qty_bne')->comment('Melbourne warehouse quantity');
            $table->integer('qty_syd')->default(0)->after('qty_mel')->comment('Sydney warehouse quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier2_products', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};
