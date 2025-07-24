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
        if (Schema::hasTable('seen_notifications')) {
            return; // Exit if the table already exists
        }
        Schema::create('seen_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('notification_id');
            $table->unsignedBigInteger('projet_id');
            $table->timestamps();

            // Add unique constraint for user_id, notification_id, projet_id combination
            $table->unique(['user_id', 'notification_id', 'projet_id'], 'unique_user_notification_projet');
            
            // Add index for user_id and projet_id combination for faster queries
            $table->index(['user_id', 'projet_id'], 'idx_user_projet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seen_notifications');
    }
};
