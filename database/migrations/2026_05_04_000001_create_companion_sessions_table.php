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
        Schema::create('companion_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('token', 64)->unique();
            $table->string('context_type')->nullable()->comment('e.g. goods_receipt');
            $table->unsignedBigInteger('context_id')->nullable();
            $table->string('photo_path')->nullable()->comment('Uploaded photo path');
            $table->timestamp('photo_uploaded_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['token', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companion_sessions');
    }
};
