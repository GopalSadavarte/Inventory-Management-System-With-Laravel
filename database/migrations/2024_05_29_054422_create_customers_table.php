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
        Schema::create('customers', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('customer_name', 200)->nullable();
            $table->string('customer_email', 200)->nullable()->unique();
            $table->string('contact', 10)->nullable();
            $table->string('customer_address', 200)->nullable();
            $table->integer('pending_amt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
