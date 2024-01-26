<?php

namespace App\Enum;

enum ModeFinancement:int
{
    case Comptant=1;
    case Crédit=2;
    case Indécis=3;

}
