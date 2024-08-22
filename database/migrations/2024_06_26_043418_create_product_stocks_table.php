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
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('stock_entry_no', false, true);
            $table->bigInteger('stock_product_id', false, true);
            $table->integer('purchase_rate');
            $table->integer('MRP');
            $table->integer('GST');
            $table->foreign('stock_entry_no')->references('stock_id')->on('stocks')->cascadeOnDelete();
            $table->foreign('stock_product_id')->references('id')->on('products');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_stocks');
    }
};
