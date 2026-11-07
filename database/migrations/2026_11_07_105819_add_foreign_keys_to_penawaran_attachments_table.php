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
        Schema::table('penawaran_attachments', function (Blueprint $table) {
            $table->foreign(['penawaran_id'], 'penawaran_attachments_ibfk_1')->references(['id'])->on('penawaran')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penawaran_attachments', function (Blueprint $table) {
            $table->dropForeign('penawaran_attachments_ibfk_1');
        });
    }
};
