<?php

namespace App\Enum;

enum OrientationEnum:int
{
   case N=1;
   case E=2;
   case S=3;
   case O=4;
   case N_E=5;
   case N_O=6;
   case N_S=7;
   case O_E=8;
   case O_S=9;
   case E_S=10;
}
