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

            $table->string('product_name')->index();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->decimal('retail_price', 12, 2)->default(0);
            $table->decimal('sale_price', 12, 2)->default(0)->nullable();

            $table->string('product_image')->nullable();

            $table->string('barcode')->unique()->nullable();
            $table->string('sku')->unique()->nullable();

            $table->boolean('track_stock')->default(true);
            
            $table->decimal('stock_quantity', 12, 2)->default(0);
            $table->decimal('min_stock_level', 12, 2)->default(0);
            
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
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
