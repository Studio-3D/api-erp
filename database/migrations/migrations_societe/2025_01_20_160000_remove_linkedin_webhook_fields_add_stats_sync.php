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
        Schema::table('linkedin_configurations', function (Blueprint $table) {
            // Remove webhook-related fields
            if (Schema::hasColumn('linkedin_configurations', 'webhook_verify_token')) {
                $table->dropColumn('webhook_verify_token');
            }
            if (Schema::hasColumn('linkedin_configurations', 'webhook_enabled')) {
                $table->dropColumn('webhook_enabled');
            }
            if (Schema::hasColumn('linkedin_configurations', 'webhook_subscriptions')) {
                $table->dropColumn('webhook_subscriptions');
            }
            
            // Add stats sync tracking field
            if (!Schema::hasColumn('linkedin_configurations', 'last_stats_sync')) {
                $table->timestamp('last_stats_sync')->nullable()->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('linkedin_configurations', function (Blueprint $table) {
            // Add webhook fields back
            $table->string('webhook_verify_token')->nullable()->after('is_active');
            $table->boolean('webhook_enabled')->default(false)->after('webhook_verify_token');
            $table->json('webhook_subscriptions')->nullable()->after('webhook_enabled');
            
            // Remove stats sync field
            $table->dropColumn('last_stats_sync');
        });
    }
};
