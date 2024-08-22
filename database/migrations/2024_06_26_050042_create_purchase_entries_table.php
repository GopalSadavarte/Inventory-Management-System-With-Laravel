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
        Schema::create('purchase_entries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('purchase_entry_id', false, true);
            $table->bigInteger('purchase_product_id', false, true);
            $table->integer('purchase_rate');
            $table->integer('MRP');
            $table->integer('GST');
            $table->foreign('purchase_product_id')->references('id')->on('products');
            $table->foreign('purchase_entry_id')->references('purchase_entry_id')->on('purchases')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_entries');
    }
};
