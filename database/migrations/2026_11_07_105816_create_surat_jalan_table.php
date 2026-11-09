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
        Schema::create('surat_jalan', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('no_sj', 30)->unique('no_sj');
            $table->date('tanggal');
            $table->integer('po_id')->nullable()->index();
            $table->integer('penawaran_id')->nullable()->index();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('nama_penerima', 150);
            $table->text('alamat_pengiriman');
            $table->string('kota_tujuan', 100)->nullable();
            $table->string('telepon_penerima', 30)->nullable();
            $table->string('nama_pengirim', 100)->nullable();
            $table->string('pic_penerima', 100)->nullable();
            $table->string('no_po', 100)->nullable();
            $table->string('kendaraan', 100)->nullable();
            $table->string('no_polisi', 20)->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['draft', 'dikirim', 'diterima', 'dikembalikan'])->default('draft');
            $table->integer('created_by')->index();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->boolean('is_history')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_jalan');
    }
};
