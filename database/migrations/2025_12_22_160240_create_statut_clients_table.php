<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enum\StatutClientEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier si la table existe déjà avant de la créer
        if (!Schema::hasTable('statut_clients')) {
            Schema::create('statut_clients', function (Blueprint $table) {
                $table->id();
                
                // Correction: 'clients' au pluriel (convention Laravel)
                $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
                
                $table->enum('statut', [
                    StatutClientEnum::Suivi_Dossier->value,
                    StatutClientEnum::Nouvelle_Avance->value,
                    StatutClientEnum::Creation_Reservation->value,
                    StatutClientEnum::Ajouter_Rdv->value,
                    StatutClientEnum::Signer_Attestation_Vente->value,
                    StatutClientEnum::Signer_Contrat_Vente->value,
                    StatutClientEnum::Remise_Cle->value,
                    StatutClientEnum::Desistement_dd->value,
                    StatutClientEnum::Desistement_dp_profit->value,
                    StatutClientEnum::Desistement_dp_co->value,
                    StatutClientEnum::Desistement_dp_partiel->value,
                    StatutClientEnum::Desistement_change_bien->value,
                    StatutClientEnum::Payer_penalite->value,
                    StatutClientEnum::Rembourser->value,
                ])->comment('0=>Suivi_Dossier, 1=>Nouvelle_Avance, 2=>Creation_Reservation, 3=>Ajouter_Rdv, 4=>Signer_Attestation_Vente, 5=>Signer_Contrat_Vente, 6=>Remise_Cle, 7=>Desistement_dd, 8=>Desistement_dp_profit, 9=>Desistement_dp_co, 10=>Desistement_dp_partiel, 11=>Desistement_change_bien, 12=>Payer_penalite, 13=>Rembourser');
                
                $table->foreignId('user_id_traite')->nullable()->constrained('users')->onDelete('cascade');
                $table->string('commentaire')->nullable();
                
                // Correction: 'visites' au pluriel
                $table->foreignId('visite_id')->nullable()->constrained('visites')->onDelete('cascade');
                
                $table->foreignId('reservation_id')->nullable()->constrained('reservations')->onDelete('cascade');
                $table->foreignId('desistement_id')->nullable()->constrained('desistements')->onDelete('cascade');
                $table->foreignId('penalite_id')->nullable()->constrained('penalites')->onDelete('cascade');
                $table->foreignId('remboursement_id')->nullable()->constrained('remboursements')->onDelete('cascade');
                
                $table->timestamps();
                $table->softDeletes();
            });
            
            // Message pour confirmer la création
            echo "Table 'statut_clients' créée avec succès.\n";
        } else {
            echo "Table 'statut_clients' existe déjà.\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statut_clients');
    }
};