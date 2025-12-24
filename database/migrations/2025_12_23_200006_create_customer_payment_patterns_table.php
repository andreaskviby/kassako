<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_payment_patterns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('customer_number');
            $table->string('customer_name');
            $table->integer('total_invoices')->default(0);
            $table->integer('paid_invoices')->default(0);
            $table->integer('avg_days_to_pay')->nullable();
            $table->integer('median_days_to_pay')->nullable();
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->decimal('revenue_percentage', 5, 2)->default(0);
            $table->string('payment_reliability');
            $table->timestamps();

            $table->unique(['team_id', 'customer_number']);
            $table->index(['team_id', 'revenue_percentage']);
        });
    }
};
