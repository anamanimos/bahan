<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->foreignId('category_id')->constrained('categories');
            $table->string('base_unit'); // e.g., Meter
            $table->string('selling_unit'); // e.g., Yard
            $table->decimal('conversion_factor', 15, 4)->default(1.0000);
            $table->text('specifications')->nullable();
            $table->string('image_path')->nullable();
            $table->string('warehouse_location')->nullable();
            $table->decimal('minimum_stock_level', 15, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
