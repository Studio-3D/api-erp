<?php

namespace App\Enum;

enum TypeDesistementProfit:int
{
    case Parents=1;
    case Fils=2;
    case Fréres=3;
    case Soeurs=4;
    case Autre=5;
}
