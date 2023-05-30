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
        Schema::create('biens', function (Blueprint $table) {
            $table->id();
            $table->string('propriete_dite_bien');
            $table->string('numero');
            $table->integer('niveau');
            $table->unsignedBigInteger('type_id');
            $table->string('orientation');
            $table->boolean('conventionne');
            $table->float('prix_unitaire');
            $table->float('prix');
            $table->float('superficie_architecte');
            $table->float('superficie_habitable');
            $table->integer('nbre_facades');
            $table->float('superficie_parking');
            $table->float('superficie_box');
            $table->float('superficie_terrasse');
            $table->float('superficie_jardin');
            $table->string('titre_foncier');
            $table->string('etat');
            $table->unsignedBigInteger('projet_id');
            $table->unsignedBigInteger('tranche_id');
            $table->unsignedBigInteger('bloc_id');
            $table->unsignedBigInteger('immeuble_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('type_id')->references('id')->on('type_biens');
            $table->foreign('projet_id')->references('id')->on('projets');
            $table->foreign('tranche_id')->references('id')->on('tranches');
            $table->foreign('bloc_id')->references('id')->on('blocs');
            $table->foreign('immeuble_id')->references('id')->on('immeubles');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biens');
    }
};
