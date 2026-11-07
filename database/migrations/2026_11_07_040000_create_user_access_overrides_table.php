<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_access_overrides')) {
            Schema::table('user_access_overrides', function (Blueprint $table): void {
                $table->integer('user_id')->change();
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });

            return;
        }

        Schema::create('user_access_overrides', function (Blueprint $table): void {
            $table->id();
            $table->integer('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('feature', 64);
            $table->string('action', 16);
            $table->boolean('allowed');
            $table->timestamps();
            $table->unique(['user_id', 'feature', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_access_overrides');
    }
};
