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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // Store info
            $table->string('store_name')->default('My Store');
            $table->string('store_address')->nullable();
            $table->string('store_phone')->nullable();

            // Currency
            $table->string('currency_symbol', 10)->default('$');

            // Tax
            $table->boolean('gst_enabled')->default(false);
            $table->decimal('gst_percentage', 5, 2)->default(0);
            $table->boolean('vat_enabled')->default(false);
            $table->decimal('vat_percentage', 5, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
