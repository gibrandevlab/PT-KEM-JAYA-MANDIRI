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
        Schema::table('penawaran', function (Blueprint $table) {
            $table->foreign(['customer_id'], 'penawaran_ibfk_1')->references(['id'])->on('customers')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['created_by'], 'penawaran_ibfk_2')->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penawaran', function (Blueprint $table) {
            $table->dropForeign('penawaran_ibfk_1');
            $table->dropForeign('penawaran_ibfk_2');
        });
    }
};
