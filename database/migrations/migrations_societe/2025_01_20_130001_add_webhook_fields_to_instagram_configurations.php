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
        Schema::table('instagram_configurations', function (Blueprint $table) {
            if (!Schema::hasColumn('instagram_configurations', 'webhook_verify_token')) {
                $table->string('webhook_verify_token')->nullable()->after('projet_id');
            }
            if (!Schema::hasColumn('instagram_configurations', 'webhook_enabled')) {
                $table->boolean('webhook_enabled')->default(false)->after('webhook_verify_token');
            }
            if (!Schema::hasColumn('instagram_configurations', 'webhook_subscriptions')) {
                $table->json('webhook_subscriptions')->nullable()->after('webhook_enabled');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instagram_configurations', function (Blueprint $table) {
            $table->dropColumn(['webhook_verify_token', 'webhook_enabled', 'webhook_subscriptions']);
        });
    }
};
