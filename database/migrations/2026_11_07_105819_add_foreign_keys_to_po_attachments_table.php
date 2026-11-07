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
        Schema::table('po_attachments', function (Blueprint $table) {
            $table->foreign(['po_id'], 'po_attachments_ibfk_1')->references(['id'])->on('purchase_order')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('po_attachments', function (Blueprint $table) {
            $table->dropForeign('po_attachments_ibfk_1');
        });
    }
};
