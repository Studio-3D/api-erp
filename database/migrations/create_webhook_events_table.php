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
        if (Schema::hasTable('webhook_events')) {
            return;
        }
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('platform'); // facebook, instagram, etc.
            $table->string('type'); // facebook_post, facebook_reaction, etc.
            $table->json('data'); // webhook payload data
            $table->boolean('processed')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['platform', 'type']);
            $table->index('processed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
