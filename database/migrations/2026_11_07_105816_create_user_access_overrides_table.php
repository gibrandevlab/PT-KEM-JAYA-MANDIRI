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
        Schema::create('user_access_overrides', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->index('user_access_overrides_user_id_foreign');
            $table->string('feature', 64);
            $table->string('action', 16);
            $table->boolean('allowed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_access_overrides');
    }
};
