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
        Schema::table('surat_jalan_items', function (Blueprint $table) {
            $table->foreign(['sj_id'], 'surat_jalan_items_ibfk_1')->references(['id'])->on('surat_jalan')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['barang_id'], 'surat_jalan_items_ibfk_2')->references(['id'])->on('barang')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan_items', function (Blueprint $table) {
            $table->dropForeign('surat_jalan_items_ibfk_1');
            $table->dropForeign('surat_jalan_items_ibfk_2');
        });
    }
};
