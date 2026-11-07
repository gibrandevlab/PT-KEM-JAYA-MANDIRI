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
        Schema::table('customer_pics', function (Blueprint $table) {
            $table->foreign(['customer_id'], 'customer_pics_ibfk_1')->references(['id'])->on('customers')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_pics', function (Blueprint $table) {
            $table->dropForeign('customer_pics_ibfk_1');
        });
    }
};
