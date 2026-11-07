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
        Schema::create('customers', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('kode_customer', 20)->unique('kode_customer');
            $table->string('nama_perusahaan', 150);
            $table->text('alamat')->nullable();
            $table->text('alamat_gudang')->nullable();
            $table->string('kota', 100)->nullable();
            $table->string('telepon', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('npwp', 50)->nullable();
            $table->string('pic', 100)->nullable()->comment('Person in charge');
            $table->string('pic_gudang', 150)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->string('pic_phone', 50)->nullable();
            $table->string('pic_email', 100)->nullable();
            $table->string('pic_gudang_phone', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
