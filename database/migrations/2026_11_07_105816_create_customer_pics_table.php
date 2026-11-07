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
        Schema::create('customer_pics', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('customer_id')->index('customer_id');
            $table->string('tipe', 50);
            $table->string('nama', 150);
            $table->string('telepon', 50)->nullable();
            $table->string('email', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_pics');
    }
};
