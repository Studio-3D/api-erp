<?php

namespace App\Enum;

use Illuminate\Validation\Rules\Enum;

Enum StatutReservationEnum:int
{
    case Validé=1;
    case Refusé=2;
    case En_Attente=3;
}
