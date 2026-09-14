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
        Schema::create('usage_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('metric', 50)->default('api_calls');
            $table->unsignedBigInteger('units');
            $table->string('idempotency_key', 128)->nullable()->unique();
            $table->dateTime('recorded_at');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['merchant_id', 'customer_id', 'recorded_at']);
        });

        Schema::create('daily_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('metric', 50)->default('api_calls');
            $table->date('usage_date');
            $table->unsignedBigInteger('total_units')->default(0);
            $table->unsignedBigInteger('event_count')->default(0);
            $table->dateTime('last_recorded_at')->nullable();
            $table->timestamps();

            $table->unique(['merchant_id', 'customer_id', 'metric', 'usage_date'], 'daily_usages_tenant_cust_metric_date_unique');
            $table->index(['customer_id', 'usage_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_usages');
        Schema::dropIfExists('usage_events');
    }
};
