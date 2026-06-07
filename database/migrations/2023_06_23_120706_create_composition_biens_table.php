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
        Schema::create('composition_biens', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bien_id')->unsigned();
            $table->foreign('bien_id')->references('id')->on('biens');
            $table->integer('nbre_chambres')->nullable();
            $table->integer('nbre_sejour')->nullable();
            $table->integer('nbre_kitchenette')->nullable();
            $table->integer('nbre_salons')->nullable();
            $table->integer('nbre_sdb')->nullable();
            $table->integer('nbre_cuisines')->nullable();
            $table->integer('nbre_halls')->nullable();
            $table->integer('nbre_terasses')->nullable();
            $table->integer('nbre_balcons')->nullable();
            $table->integer('nbre_buanderies')->nullable();
            $table->integer('nbre_placards')->nullable();
            $table->integer('nbre_receptions')->nullable();
            $table->timestamps();
            $table->softDeletes();



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('composition_biens');
    }
};
