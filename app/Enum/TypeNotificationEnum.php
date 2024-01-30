<?php

namespace App\Enum;

enum TypeNotificationEnum:int
{
   case Sms=1;
   case Appel=2;
   case Email=3;
}
