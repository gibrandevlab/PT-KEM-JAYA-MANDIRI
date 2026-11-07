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
        Schema::table('penawaran_items', function (Blueprint $table) {
            $table->foreign(['penawaran_id'], 'penawaran_items_ibfk_1')->references(['id'])->on('penawaran')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['barang_id'], 'penawaran_items_ibfk_2')->references(['id'])->on('barang')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penawaran_items', function (Blueprint $table) {
            $table->dropForeign('penawaran_items_ibfk_1');
            $table->dropForeign('penawaran_items_ibfk_2');
        });
    }
};
