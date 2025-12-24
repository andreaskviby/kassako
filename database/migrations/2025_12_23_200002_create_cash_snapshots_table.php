<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->date('snapshot_date');
            $table->decimal('cash_balance', 15, 2)->default(0);
            $table->decimal('accounts_receivable', 15, 2)->default(0);
            $table->decimal('accounts_payable', 15, 2)->default(0);
            $table->integer('runway_days')->nullable();
            $table->decimal('avg_daily_burn', 15, 2)->nullable();
            $table->decimal('avg_daily_income', 15, 2)->nullable();
            $table->json('monthly_forecast')->nullable();
            $table->json('insights')->nullable();
            $table->timestamps();

            $table->unique(['team_id', 'snapshot_date']);
            $table->index(['team_id', 'snapshot_date']);
        });
    }
};
