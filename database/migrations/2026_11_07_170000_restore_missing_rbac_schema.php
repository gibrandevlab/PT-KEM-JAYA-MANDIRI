<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'remember_token')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->rememberToken();
            });
        }

        if (! Schema::hasTable('user_access_overrides')) {
            Schema::create('user_access_overrides', function (Blueprint $table): void {
                $table->id();
                $table->integer('user_id');
                $table->string('feature', 64);
                $table->string('action', 16);
                $table->boolean('allowed');
                $table->timestamps();
                $table->unique(['user_id', 'feature', 'action']);
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_access_overrides');

        if (Schema::hasColumn('users', 'remember_token')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('remember_token');
            });
        }
    }
};
