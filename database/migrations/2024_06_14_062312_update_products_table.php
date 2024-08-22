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
            $table->after('product_id', function (Blueprint $table) {
                $table->bigInteger('group_no', false, true);
                $table->bigInteger('sub_group_no', false, true);
                $table->foreign('group_no')->references('group_id')->on('groups');
                $table->foreign('sub_group_no')->references('sub_group_id')->on('sub_groups');
            });
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
