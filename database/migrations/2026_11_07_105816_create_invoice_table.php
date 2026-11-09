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
        Schema::create('invoice', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('no_invoice', 30)->unique('no_invoice');
            $table->date('tanggal');
            $table->date('jatuh_tempo')->nullable();
            $table->integer('sj_id')->nullable()->index();
            $table->integer('po_id')->nullable()->index();
            $table->integer('penawaran_id')->nullable()->index();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('nama_customer', 150);
            $table->text('alamat_customer')->nullable();
            $table->string('telepon_customer', 30)->nullable();
            $table->string('npwp', 50)->nullable();
            $table->string('no_faktur_pajak', 100)->nullable();
            $table->string('term_of_payment')->nullable();
            $table->text('catatan')->nullable();
            $table->integer('bank_account_id')->nullable();
            $table->decimal('subtotal', 20)->nullable()->default(0);
            $table->decimal('diskon_persen', 5)->default(0);
            $table->decimal('diskon_nominal', 20)->nullable()->default(0);
            $table->decimal('ppn_persen', 5)->default(11);
            $table->decimal('ppn_nominal', 20)->nullable()->default(0);
            $table->boolean('is_dpp')->default(false);
            $table->decimal('dpp_nominal', 15)->default(0);
            $table->decimal('total', 20)->nullable()->default(0);
            $table->enum('status', ['draft', 'terkirim', 'dibayar', 'jatuh_tempo'])->default('draft');
            $table->integer('created_by')->index();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->boolean('is_revisi')->nullable()->default(false);
            $table->string('revisi_ke', 50)->nullable();
            $table->string('tipe_invoice', 50)->nullable();
            $table->string('po_number', 100)->nullable();
            $table->date('tanggal_po')->nullable();
            $table->string('shipment_number', 100)->nullable();
            $table->decimal('dp_persen', 5)->nullable()->default(0);
            $table->decimal('dp_nominal', 15)->nullable()->default(0);
            $table->boolean('is_history')->nullable()->default(false);
            $table->integer('dp_ref_invoice_id')->nullable();
            $table->string('status_payment', 20)->nullable()->default('Unpaid');
            $table->date('tanggal_payment')->nullable();
            $table->date('tanggal_kirim_dokumen')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice');
    }
};
