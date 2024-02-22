<?php

namespace App\Enum;

enum StatutVisiteEnum:int
{
   case Pré_Réservation=1;
   case Vendu=2;
   case Pré_Réservation_Perdu=3;
   case Réservation_Perdu=4;
   case Pré_Réservation_Vendu=5;
}
