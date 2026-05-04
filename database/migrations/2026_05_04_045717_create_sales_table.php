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
        Schema::create('sales', function (Blueprint $row) {
            $row->id();
            $row->string('invoice_number')->unique();
            $row->foreignId('customer_id')->constrained()->onDelete('cascade');
            $row->date('sale_date');
            $row->decimal('total_amount', 15, 2)->default(0);
            $row->string('status')->default('Paid'); // Paid, Unpaid, Cancelled
            $row->string('payment_method')->nullable();
            $row->text('notes')->nullable();
            $row->foreignId('created_by')->nullable()->constrained('users');
            $row->softDeletes();
            $row->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
