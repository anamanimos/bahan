<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->unique(); // GR-YYYYMMDD-NNN
            $table->date('received_date');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->string('invoice_number')->nullable();
            $table->string('invoice_photo_path')->nullable();
            $table->foreignId('purchase_requisition_id')->nullable()->constrained('purchase_requisitions');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_receipts');
    }
};
