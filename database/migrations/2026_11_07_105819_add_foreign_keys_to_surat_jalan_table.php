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
        Schema::table('surat_jalan', function (Blueprint $table) {
            $table->foreign(['po_id'], 'surat_jalan_ibfk_1')->references(['id'])->on('purchase_order')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['penawaran_id'], 'surat_jalan_ibfk_2')->references(['id'])->on('penawaran')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['created_by'], 'surat_jalan_ibfk_3')->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan', function (Blueprint $table) {
            $table->dropForeign('surat_jalan_ibfk_1');
            $table->dropForeign('surat_jalan_ibfk_2');
            $table->dropForeign('surat_jalan_ibfk_3');
        });
    }
};
