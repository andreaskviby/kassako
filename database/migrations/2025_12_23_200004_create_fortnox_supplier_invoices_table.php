<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fortnox_supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('fortnox_id')->index();
            $table->string('document_number')->nullable();
            $table->string('supplier_name')->nullable();
            $table->string('supplier_number')->nullable();
            $table->decimal('total', 15, 2);
            $table->string('currency', 3)->default('SEK');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->string('status');
            $table->timestamps();

            $table->unique(['team_id', 'fortnox_id']);
            $table->index(['team_id', 'status']);
            $table->index(['team_id', 'due_date']);
        });
    }
};
