<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->unique();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('goods_receipt_item_id')->nullable()->constrained('goods_receipt_items');
            $table->decimal('initial_quantity', 15, 2);
            $table->decimal('remaining_quantity', 15, 2);
            $table->decimal('unit_cost', 15, 2);
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};
