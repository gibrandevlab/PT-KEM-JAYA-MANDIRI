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
        Schema::table('purchase_order', function (Blueprint $table) {
            $table->foreign(['po_customer_id'], 'fk_po_customer_id')->references(['id'])->on('po_customers')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['penawaran_id'], 'purchase_order_ibfk_1')->references(['id'])->on('penawaran')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['customer_id'], 'purchase_order_ibfk_2')->references(['id'])->on('customers')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['vendor_id'], 'purchase_order_ibfk_3')->references(['id'])->on('vendors')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['created_by'], 'purchase_order_ibfk_4')->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order', function (Blueprint $table) {
            $table->dropForeign('fk_po_customer_id');
            $table->dropForeign('purchase_order_ibfk_1');
            $table->dropForeign('purchase_order_ibfk_2');
            $table->dropForeign('purchase_order_ibfk_3');
            $table->dropForeign('purchase_order_ibfk_4');
        });
    }
};
