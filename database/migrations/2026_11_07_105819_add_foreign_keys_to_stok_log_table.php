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
        Schema::table('stok_log', function (Blueprint $table) {
            $table->foreign(['barang_id'], 'stok_log_ibfk_1')->references(['id'])->on('barang')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['created_by'], 'stok_log_ibfk_2')->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stok_log', function (Blueprint $table) {
            $table->dropForeign('stok_log_ibfk_1');
            $table->dropForeign('stok_log_ibfk_2');
        });
    }
};
