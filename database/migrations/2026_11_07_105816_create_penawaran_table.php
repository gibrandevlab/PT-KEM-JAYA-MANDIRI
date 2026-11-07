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
        Schema::create('penawaran', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('no_penawaran', 30)->unique('no_penawaran');
            $table->date('tanggal');
            $table->integer('customer_id')->nullable()->index('customer_id');
            $table->string('nama_customer', 150);
            $table->text('alamat_customer')->nullable();
            $table->text('alamat_customer_2')->nullable();
            $table->string('telepon_customer', 30)->nullable();
            $table->string('npwp_customer', 50)->nullable();
            $table->string('perihal')->nullable();
            $table->string('term_of_delivery')->nullable();
            $table->string('delivery_time')->nullable();
            $table->string('term_of_payment')->nullable();
            $table->text('catatan')->nullable();
            $table->date('berlaku_sampai')->nullable();
            $table->decimal('subtotal', 20)->nullable()->default(0);
            $table->decimal('diskon_persen', 5)->default(0);
            $table->decimal('diskon_nominal', 20)->nullable()->default(0);
            $table->decimal('ppn_persen', 5)->default(11);
            $table->decimal('ppn_nominal', 20)->nullable()->default(0);
            $table->decimal('total', 20)->nullable()->default(0);
            $table->enum('status', ['draft', 'terkirim', 'disetujui', 'ditolak', 'expired', 'selesai'])->default('draft');
            $table->integer('created_by')->index('created_by');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->string('kode_sdk', 100)->nullable();
            $table->boolean('use_sdk')->nullable()->default(false);
            $table->string('com_contact', 100)->nullable();
            $table->boolean('is_revisi')->nullable()->default(false);
            $table->string('revisi_ke', 50)->nullable();
            $table->string('up', 100)->nullable();
            $table->string('mobile', 50)->nullable();
            $table->string('email_up', 100)->nullable();
            $table->boolean('is_history')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penawaran');
    }
};
