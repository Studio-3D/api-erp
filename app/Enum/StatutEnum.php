<?php

namespace App\Enum;

enum StatutEnum:int
{
   case PRE_RESERVATION=1;
   case VENDU=2;
   case PRE_RESERVATION_PERDU=3;
}
