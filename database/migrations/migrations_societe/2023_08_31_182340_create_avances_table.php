<?php

use App\Enum\ModePaiement;
use App\Enum\Status;
use App\Enum\Statut;
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
        Schema::create('avances', function (Blueprint $table) {
            $table->id();
            $table->float('montant');
            $table->date('date_de_reglement');
            $table->enum('mode_paiement',[ModePaiement::ESPECE->name,ModePaiement::CHEQUE->name,ModePaiement::CHEQUE_BANQUE->name,ModePaiement::CHEQUE_CERTIFIE->name,ModePaiement::VIREMENT->name,ModePaiement::VERSEMENT->name]);
            $table->date('echance');
            $table->boolean('sr')->default(false);
            $table->enum('statut',[Statut::EN_ATTEND->name,Statut::REFUSER->name,Statut::VALIDER->name]);
            $table->foreignId('banque_id')->nullable()->constrained('banques')->onDelete('cascade');
            $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avances');
    }
};
