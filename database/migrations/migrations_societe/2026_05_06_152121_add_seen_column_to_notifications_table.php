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
        Schema::table('notifications', function (Blueprint $table) {
            // Check if column doesn't exist, then add it
            if (!Schema::hasColumn('notifications', 'seen')) {
                $table->json('seen')->nullable();
            } else {
                // If column exists but is not JSON, change it
                $table->json('seen')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Drop the column if needed
            if (Schema::hasColumn('notifications', 'seen')) {
                $table->dropColumn('seen');
            }
        });
    }
};
