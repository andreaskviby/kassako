<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Modify subscriptions table to use team_id instead of user_id
        Schema::table('subscriptions', function (Blueprint $table) {
            // Drop the existing index on user_id
            $table->dropIndex('subscriptions_user_id_stripe_status_index');

            // Rename user_id to team_id
            $table->renameColumn('user_id', 'team_id');
        });

        // Re-add index with new column name
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['team_id', 'stripe_status']);
        });
    }
};
