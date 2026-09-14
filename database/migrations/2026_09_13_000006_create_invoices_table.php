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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->dateTime('period_start');
            $table->dateTime('period_end');
            $table->decimal('base_amount', 10, 2)->default(0.00);
            $table->decimal('overage_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->decimal('proration_ratio', 6, 4)->default(1.0000);
            $table->string('status', 20)->default('issued'); // draft, issued, paid, void
            $table->string('currency', 3)->default('USD');
            $table->dateTime('issued_at');
            $table->timestamps();

            $table->index(['merchant_id', 'status']);
            $table->index(['customer_id', 'period_end']);
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('type', 30); // base_fee, allowance, overage, credit
            $table->text('description');
            $table->unsignedBigInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 4)->default(0.0000);
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->timestamps();

            $table->index(['invoice_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
