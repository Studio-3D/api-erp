<?php

namespace App\Enum;

enum InteretEnum:int
{
    case Intéressé=1;
    case Réceptif=2;
    case Perdu=3;
    //4 injoignable for appel
    case Suivi_dossier=5;
}
