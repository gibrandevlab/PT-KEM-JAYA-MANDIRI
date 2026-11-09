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
        Schema::create('po_attachments', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('po_id')->index();
            $table->string('file_name');
            $table->string('file_path');
            $table->timestamp('uploaded_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_attachments');
    }
};
