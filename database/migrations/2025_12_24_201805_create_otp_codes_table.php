<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('email')->index();
            $table->string('code_hash', 255);
            $table->string('purpose', 50)->default('login');
            $table->timestamp('expires_at');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->timestamps();

            $table->index(['email', 'purpose', 'expires_at']);
            $table->index('expires_at');
        });

        Schema::create('otp_rate_limits', function (Blueprint $table) {
            $table->id();
            $table->string('identifier', 255);
            $table->string('type', 20);
            $table->unsignedInteger('attempts')->default(1);
            $table->timestamp('blocked_until')->nullable();
            $table->timestamp('window_start');
            $table->timestamps();

            $table->unique(['identifier', 'type']);
            $table->index('blocked_until');
        });
    }
};
