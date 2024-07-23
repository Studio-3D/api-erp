<?php

namespace App\Enum;

use Illuminate\Validation\Rules\Enum;

Enum StatutRdvEnum:int
{
    case En_Attente=0;
    case Validé=1;
    case Refusé=2;
    case Raté=3;
}
