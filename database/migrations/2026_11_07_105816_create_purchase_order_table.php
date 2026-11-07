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
        Schema::create('purchase_order', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('no_po', 30)->unique('no_po');
            $table->string('no_po_customer', 100)->nullable();
            $table->enum('jenis', ['dari_customer', 'ke_vendor'])->comment('PO dari Customer ke KJM atau PO dari KJM ke Vendor');
            $table->date('tanggal');
            $table->integer('penawaran_id')->nullable()->index('penawaran_id')->comment('Referensi ke penawaran (untuk PO dari customer)');
            $table->integer('customer_id')->nullable()->index('customer_id');
            $table->integer('po_customer_id')->nullable()->index('fk_po_customer_id');
            $table->integer('vendor_id')->nullable()->index('vendor_id');
            $table->string('nama_perusahaan', 150);
            $table->string('term_of_delivery')->nullable();
            $table->string('delivery_time')->nullable();
            $table->string('term_of_payment')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon', 30)->nullable();
            $table->date('tanggal_kirim')->nullable();
            $table->text('catatan')->nullable();
            $table->decimal('subtotal', 20)->nullable()->default(0);
            $table->decimal('ppn_persen', 5)->default(11);
            $table->decimal('ppn_nominal', 20)->nullable()->default(0);
            $table->decimal('total', 20)->nullable()->default(0);
            $table->enum('status', ['draft', 'diproses', 'selesai', 'dibatalkan'])->default('draft');
            $table->integer('created_by')->index('created_by');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->string('kategori_po', 50)->nullable()->default('Dalam Negeri');
            $table->string('pic', 100)->nullable();
            $table->string('currency', 20)->nullable()->default('IDR');
            $table->decimal('diskon_persen', 5)->nullable()->default(0);
            $table->decimal('diskon_nominal', 15)->nullable()->default(0);
            $table->boolean('is_history')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order');
    }
};
