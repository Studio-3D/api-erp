<?php

use App\Enum\InteretEnum;
use App\Enum\StatutVisiteEnum;
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
        Schema::create('traitement_freins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('frein_id')->constrained('freins')->onDelete('cascade');
            $table->foreignId('visite_id')->nullable()->constrained('visites')->onDelete('cascade');
            $table->foreignId('origin_id')->nullable()->constrained('visites')->onDelete('cascade');
            $table->enum('interet',[InteretEnum::Intéressé->value,InteretEnum::Réceptif->value,InteretEnum::Perdu->value]);
            $table->enum('statut',[StatutVisiteEnum::Pré_Réservation->value,StatutVisiteEnum::Vendu->value,StatutVisiteEnum::Pré_Réservation_Perdu->value,StatutVisiteEnum::Réservation_Perdu->value,StatutVisiteEnum::Pré_Réservation_Vendu->value])->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('bien_id')->nullable()->constrained('biens')->onDelete('cascade');
            $table->string('commentaire')->nullable(); // car en peut recoit des vistes sans commentaire.
            $table->timestamp('date')->nullable();
            $table->integer('relance_rdv_id')->nullable();
            $table->timestamps();
            $table->softDeletes(); // si on veut de garder historique de la visite.
            $table->index(['frein_id','visite_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traitement_freins');
    }
};
