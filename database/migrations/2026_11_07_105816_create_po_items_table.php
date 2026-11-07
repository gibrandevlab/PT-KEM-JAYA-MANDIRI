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
        Schema::create('po_items', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('po_id')->index('po_id');
            $table->integer('barang_id')->nullable()->index('barang_id');
            $table->string('kode_barang', 50)->nullable();
            $table->text('nama_barang')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('satuan', 30);
            $table->decimal('qty', 15)->default(1);
            $table->decimal('harga_satuan', 20)->nullable()->default(0);
            $table->decimal('subtotal', 20)->nullable()->default(0);
            $table->integer('urutan')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_items');
    }
};
