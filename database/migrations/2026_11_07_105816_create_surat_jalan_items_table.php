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
        Schema::create('surat_jalan_items', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('sj_id')->index('sj_id');
            $table->integer('barang_id')->nullable()->index('barang_id');
            $table->string('kode_barang', 50)->nullable();
            $table->text('nama_barang')->nullable();
            $table->string('satuan', 30);
            $table->decimal('qty', 15)->default(1);
            $table->text('keterangan')->nullable();
            $table->integer('urutan')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_jalan_items');
    }
};
