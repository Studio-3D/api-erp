<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('notifications')) {
            return;
        }
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('projet_id')->nullable();
            $table->string('type'); // 'facebook_like', 'instagram_comment', etc.
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Store webhook data
            $table->timestamp('date');
            $table->boolean('read')->default(false);
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['user_id', 'projet_id', 'deleted_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
