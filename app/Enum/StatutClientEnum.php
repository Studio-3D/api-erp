<?php

namespace App\Enum;

enum StatutClientEnum: int
{
    case Suivi_Dossier = 0;
    case Nouvelle_Avance = 1;
    case Creation_Reservation = 2;
    case Ajouter_Rdv = 3;
    case Signer_Attestation_Vente = 4;
    case Signer_Contrat_Vente = 5;
    case Remise_Cle = 6;
    case Desistement_dd = 7;
    case Desistement_dp_profit = 8;
    case Desistement_dp_co = 9;
    case Desistement_dp_partiel = 10;
    case Desistement_change_bien = 11;
    case Payer_penalite = 12;
    case Rembourser = 13;


   
}
