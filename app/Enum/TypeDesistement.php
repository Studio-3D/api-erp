<?php

namespace App\Enum;

enum TypeDesistement:int
{
    case Désistement_Définitif=1;
    case Désistement_Au_Profit=2;
    case Changement_De_Bien=3;
}
