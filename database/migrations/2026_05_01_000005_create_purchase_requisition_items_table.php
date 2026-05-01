<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_requisition_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_requisition_id')->constrained('purchase_requisitions')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
            $table->decimal('requested_quantity', 15, 2);
            $table->string('unit');
            $table->decimal('estimated_unit_price', 15, 2);
            $table->enum('context', ['Stock', 'Order'])->default('Stock');
            $table->string('erp_order_reference')->nullable(); // format YYMMNNN
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requisition_items');
    }
};
