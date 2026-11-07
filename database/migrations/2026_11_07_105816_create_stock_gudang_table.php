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
        Schema::create('stock_gudang', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('barang_id');
            $table->string('nama_barang');
            $table->string('kategori', 100)->nullable();
            $table->string('uom', 50)->nullable();
            $table->integer('stok_masuk')->nullable()->default(0);
            $table->integer('barang_keluar')->nullable()->default(0);
            $table->integer('stok_update')->nullable()->default(0);
            $table->date('tanggal_keluar')->nullable();
            $table->string('pic', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_gudang');
    }
};
