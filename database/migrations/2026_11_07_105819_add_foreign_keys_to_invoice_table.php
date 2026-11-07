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
        Schema::table('invoice', function (Blueprint $table) {
            $table->foreign(['sj_id'], 'invoice_ibfk_1')->references(['id'])->on('surat_jalan')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['po_id'], 'invoice_ibfk_2')->references(['id'])->on('purchase_order')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['penawaran_id'], 'invoice_ibfk_3')->references(['id'])->on('penawaran')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['created_by'], 'invoice_ibfk_4')->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice', function (Blueprint $table) {
            $table->dropForeign('invoice_ibfk_1');
            $table->dropForeign('invoice_ibfk_2');
            $table->dropForeign('invoice_ibfk_3');
            $table->dropForeign('invoice_ibfk_4');
        });
    }
};
