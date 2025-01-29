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
            $table->string('commentaire')->nullable(); // car en peut recoit des vistes sans commentaire.
            $table->enum('interet',[InteretEnum::Intéressé->value,InteretEnum::Réceptif->value,InteretEnum::Perdu->value])->comment('1=>interesse 2=>recpetif 3=>perdu 4=>injoignable');
            $table->enum('statut',[StatutVisiteEnum::Pré_Réservation->value,StatutVisiteEnum::Vendu->value,StatutVisiteEnum::Pré_Réservation_Perdu->value,StatutVisiteEnum::Réservation_Perdu->value,StatutVisiteEnum::Pré_Réservation_Vendu->value])->nullable()->comment('1=>Pre reservation 2=>Vendu 3=>Pre reservation perdu 4=> reservation perdu  5=>Pré_Réservation_Vendu');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('bien_id')->nullable()->constrained('biens')->onDelete('cascade');
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
