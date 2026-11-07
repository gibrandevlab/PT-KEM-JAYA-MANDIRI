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
        Schema::create('barang', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('kode_barang', 30)->unique('kode_barang');
            $table->text('nama_barang')->nullable();
            $table->integer('kategori_id')->nullable()->index('fk_barang_kategori');
            $table->string('satuan', 30)->default('Pcs');
            $table->decimal('stok', 15)->default(0);
            $table->decimal('stok_minimum', 15)->default(0);
            $table->decimal('harga_beli', 20)->nullable()->default(0);
            $table->string('tahun', 10)->nullable();
            $table->decimal('harga_satuan', 20)->nullable()->default(0);
            $table->text('deskripsi')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->unique(['kode_barang'], 'kode_barang_2');
            $table->unique(['kode_barang'], 'kode_barang_3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
