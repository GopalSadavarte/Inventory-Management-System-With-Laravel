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
        Schema::create('expiry_entries', function (Blueprint $table) {
            $table->id()->primary();
            $table->foreignId('expiry_entry_no')->references('id')->on('expiries')->cascadeOnDelete();
            $table->foreignId('product_id')->references('id')->on('products');
            $table->integer('returnQuantity');
            $table->integer('rate');
            $table->integer('MRP');
            $table->integer('GST');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expiry_entries');
    }
};
