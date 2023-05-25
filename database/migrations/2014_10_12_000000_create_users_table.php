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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->tinyInteger('is_admin')->default(0);
            $table->string('gender')->nullable();
            $table->string('phone')->nullable();
            $table->string('photo')->nullable();
            $table->string('password');
            $table->integer('nb_appel_recu')->default(0);
            $table->integer('nb_appel_traite')->default(0);
            $table->string('remember_token')->nullable();
            $table->string('cin')->unique();
            $table->date('date_embauche');
            $table->string('niveau_etude');
            $table->string('adresse')->nullable();
            $table->integer('cnss')->nullable();
            $table->integer('enable')->default(1);
            $table->string('fonction')->nullable();
            $table->integer('solde_conge')->default(0);
            $table->integer('nb_dossier_notaire')->default(0);
            $table->tinyInteger('etat')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
