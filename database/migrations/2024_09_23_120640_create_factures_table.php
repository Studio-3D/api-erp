<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enum\ModePaiement;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projet_id')->constrained('projets')->onDelete('cascade');
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->onDelete('cascade');
            $table->foreignId('decompte_id')->constrained('decomptes')->onDelete('cascade');
            $table->date('date_facture');
            $table->string('num_facture');
            $table->string('piece_jointe');
            $table->double('ht',20,2);
            $table->double('taux_tva',20,2);
            $table->double('retenue_garantie',20,2);
            $table->double('tva',20,2);
            $table->double('ttc',20,2);
            $table->date('date_paiement');
            $table->enum('mode_paiement',[ModePaiement::Espèce->value,ModePaiement::Chèque->value,ModePaiement::Chèque_Banque->value,ModePaiement::Chèque_Certifié->value,ModePaiement::Virement->value,ModePaiement::Versement->value]);
            $table->string('pj_paiement')->nullable();
            $table->bigInteger('numero_paiement')->nullable();
            $table->foreignId('banque_id')->nullable()->constrained('banques')->onDelete('cascade');
            $table->double('montant',20,2);
            $table->date('echeance')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
