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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->nullable();
            $table->string('asin')->nullable();
            $table->string('ean')->nullable();
            $table->string('isbn')->nullable();
            $table->string('upc')->nullable();
            $table->string('name')->nullable();
            $table->text('shortdescription')->nullable();
            $table->text('longdescription')->nullable();
            $table->string('category1')->nullable();
            $table->string('category2')->nullable();
            $table->string('category3')->nullable();
            $table->string('category4')->nullable();
            $table->decimal('costprice', 10, 2)->nullable();
            $table->decimal('saleprice', 10, 2)->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->string('imagesrc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
