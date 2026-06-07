<?php

namespace App\Enum;

enum OrientationEnum:int
{
    // Orientations de base (4)
    case N = 1;           // Nord
    case E = 2;           // Est
    case S = 3;           // Sud
    case O = 4;           // Ouest

    // Orientations diagonales (4)
    case N_E = 5;         // Nord-Est
    case N_O = 6;         // Nord-Ouest
    case S_E = 7;         // Sud-Est
    case S_O = 8;         // Sud-Ouest

    // Nouvelles orientations du fichier Excel (8)
    case NORD_SUD = 9;        // Nord/Sud
    case NORD_OUEST = 10;     // Nord-Ouest
    case SUD_EST = 11;        // Sud-Est
    case EST_OUEST = 12;      // Est/Ouest
    case NO_SE = 13;          // Nord-Ouest / Sud-Est
    case NORD_SUD_OUEST = 14; // Nord/Sud/Ouest
    case NORD_SUD_EST = 15;   // Nord/Sud/Est
    case NORD_EST_OUEST = 16; // Nord/Est/Ouest
}
