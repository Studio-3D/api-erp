<?php

namespace App\Enum;

enum TypeNotificationEnum:int
{
   case SMS=1;
   case WHATSAPP=2;
   case APPEL=3;
   case EMAIL=4;


}
