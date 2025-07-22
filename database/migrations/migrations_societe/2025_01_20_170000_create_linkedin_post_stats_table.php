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
        Schema::create('linkedin_post_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('configuration_id');
            $table->string('linkedin_post_id');
            $table->text('post_content')->nullable();
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->timestamp('post_created_at')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();
            
            $table->foreign('configuration_id')->references('id')->on('linkedin_configurations')->onDelete('cascade');
            $table->unique(['configuration_id', 'linkedin_post_id']);
            $table->index(['configuration_id', 'post_created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linkedin_post_stats');
    }
};
