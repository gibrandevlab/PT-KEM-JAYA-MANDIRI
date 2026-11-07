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
        Schema::create('stok_log', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('barang_id')->index('barang_id');
            $table->enum('jenis', ['masuk', 'keluar', 'adjustment']);
            $table->decimal('qty', 15);
            $table->decimal('stok_sebelum', 15);
            $table->decimal('stok_sesudah', 15);
            $table->string('referensi_tipe', 50)->nullable()->comment('surat_jalan, invoice, adjustment');
            $table->integer('referensi_id')->nullable();
            $table->text('keterangan')->nullable();
            $table->integer('created_by')->index('created_by');
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_log');
    }
};
