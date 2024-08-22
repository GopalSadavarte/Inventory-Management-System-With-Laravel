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
        Schema::table('customer_products', function (Blueprint $table) {
            $table->after('p_id', function (Blueprint $table) {
                $table->integer('newQuantity')->nullable();
                $table->integer('newRate')->nullable();
                $table->integer('newMRP')->nullable();
                $table->float('newDiscount', 2)->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_products');
    }
};
