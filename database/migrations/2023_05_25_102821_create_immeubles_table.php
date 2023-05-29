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
        Schema::create('immeubles', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('titre_foncier');
            $table->unsignedBigInteger('projet_id');
            $table->unsignedBigInteger('tranche_id');
            $table->unsignedBigInteger('bloc_id');
            // Add other columns as needed
            $table->timestamps();

            $table->foreign('projet_id')->references('id')->on('projets');
            $table->foreign('tranche_id')->references('id')->on('tranches');
            $table->foreign('bloc_id')->references('id')->on('blocs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('immeubles');
    }
};
