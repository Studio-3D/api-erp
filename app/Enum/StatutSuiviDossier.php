<?php

namespace App\Enum;

enum StatutSuiviDossier:int
{
    case Nouvelle_avance=1;
    case Suivi_Avancement_travaux=2;
    case Demande_documents=3;
    case Autre=4;

}
