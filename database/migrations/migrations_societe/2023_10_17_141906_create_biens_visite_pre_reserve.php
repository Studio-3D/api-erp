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
        Schema::create('biens_visite_pre_reserve', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('code_pre_reserve');
            $table->dateTime('date_pre_reserve');
            $table->bigInteger('bien_id')->unsigned();
            $table->foreign('bien_id')->references('id')->on('biens');
            $table->bigInteger('visite_id')->unsigned();
            $table->foreign('visite_id')->references('id')->on('visites');
            $table->timestamps();
            $table->softDeletes();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biens_visite_pre_reserve');
    }
};
