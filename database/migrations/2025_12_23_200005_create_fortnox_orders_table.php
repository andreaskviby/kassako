<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fortnox_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('fortnox_id')->index();
            $table->string('document_number')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_number')->nullable();
            $table->decimal('total', 15, 2);
            $table->string('currency', 3)->default('SEK');
            $table->date('order_date');
            $table->date('delivery_date')->nullable();
            $table->string('status');
            $table->timestamps();

            $table->unique(['team_id', 'fortnox_id']);
            $table->index(['team_id', 'status']);
        });
    }
};
