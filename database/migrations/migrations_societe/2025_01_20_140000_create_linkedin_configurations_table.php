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
        Schema::create('linkedin_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('linkedin_page_id');
            $table->string('linkedin_page_name');
            $table->longText('access_token');
            $table->unsignedBigInteger('projet_id');
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
            
            $table->foreign('projet_id')->references('id')->on('projets')->onDelete('cascade');
            $table->unique(['projet_id', 'deleted_at'], 'unique_project_linkedin_config');
            $table->index(['projet_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linkedin_configurations');
    }
};
