<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add encryption support columns to all sensitive data tables.
 *
 * Strategy:
 * - Add encrypted_data column (stores all sensitive fields as encrypted JSON)
 * - Add encryption_iv column (unique per record for AES-GCM)
 * - Add encryption_version column (track which key version was used)
 * - Keep non-sensitive columns in plaintext for querying (dates, IDs, status)
 *
 * What gets encrypted vs. what stays in plaintext:
 * - Encrypted: Financial amounts, customer names, tokens, personal data
 * - Plaintext: team_id, dates (for querying), status flags, foreign keys
 */
return new class extends Migration
{
    public function up(): void
    {
        // fortnox_connections - tokens are highly sensitive
        Schema::table('fortnox_connections', function (Blueprint $table) {
            $table->text('encrypted_data')->nullable()->after('is_active');
            $table->string('encryption_iv', 24)->nullable();
            $table->string('encryption_auth_tag', 32)->nullable();
            $table->unsignedInteger('encryption_version')->default(0);
            $table->boolean('is_encrypted')->default(false);
        });

        // cash_snapshots - financial data is sensitive
        Schema::table('cash_snapshots', function (Blueprint $table) {
            $table->text('encrypted_data')->nullable()->after('insights');
            $table->string('encryption_iv', 24)->nullable();
            $table->string('encryption_auth_tag', 32)->nullable();
            $table->unsignedInteger('encryption_version')->default(0);
            $table->boolean('is_encrypted')->default(false);
        });

        // fortnox_invoices - customer info and amounts are sensitive
        Schema::table('fortnox_invoices', function (Blueprint $table) {
            $table->text('encrypted_data')->nullable()->after('is_credit');
            $table->string('encryption_iv', 24)->nullable();
            $table->string('encryption_auth_tag', 32)->nullable();
            $table->unsignedInteger('encryption_version')->default(0);
            $table->boolean('is_encrypted')->default(false);
        });

        // fortnox_supplier_invoices - supplier info and amounts are sensitive
        Schema::table('fortnox_supplier_invoices', function (Blueprint $table) {
            $table->text('encrypted_data')->nullable()->after('status');
            $table->string('encryption_iv', 24)->nullable();
            $table->string('encryption_auth_tag', 32)->nullable();
            $table->unsignedInteger('encryption_version')->default(0);
            $table->boolean('is_encrypted')->default(false);
        });

        // fortnox_orders - customer info and amounts are sensitive
        Schema::table('fortnox_orders', function (Blueprint $table) {
            $table->text('encrypted_data')->nullable()->after('status');
            $table->string('encryption_iv', 24)->nullable();
            $table->string('encryption_auth_tag', 32)->nullable();
            $table->unsignedInteger('encryption_version')->default(0);
            $table->boolean('is_encrypted')->default(false);
        });

        // customer_payment_patterns - customer names and revenue data
        Schema::table('customer_payment_patterns', function (Blueprint $table) {
            $table->text('encrypted_data')->nullable()->after('payment_reliability');
            $table->string('encryption_iv', 24)->nullable();
            $table->string('encryption_auth_tag', 32)->nullable();
            $table->unsignedInteger('encryption_version')->default(0);
            $table->boolean('is_encrypted')->default(false);
        });
    }
};
