<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->unique(); // PR-YYYYMMDD-NNN
            $table->foreignId('created_by_user_id')->nullable(); // For future SSO integration
            $table->enum('status', ['Draft', 'Submitted', 'Partially Approved', 'Approved', 'Rejected', 'Completed'])->default('Draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requisitions');
    }
};
