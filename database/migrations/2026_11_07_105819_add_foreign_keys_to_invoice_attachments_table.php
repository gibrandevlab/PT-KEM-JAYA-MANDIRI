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
        Schema::table('invoice_attachments', function (Blueprint $table) {
            $table->foreign(['invoice_id'], 'invoice_attachments_ibfk_1')->references(['id'])->on('invoice')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_attachments', function (Blueprint $table) {
            $table->dropForeign('invoice_attachments_ibfk_1');
        });
    }
};
