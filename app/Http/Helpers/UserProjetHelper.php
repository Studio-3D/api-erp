<?php

namespace  App\Http\Helpers;

use App\Models\UserProjet;


class UserProjetHelper
{
    public static function createUserProjet($projet_id, $user_id)

    {                  
        $UserProjet = new UserProjet();
        $UserProjet->setConnection('temp');
        $UserProjet->projet_id= $projet_id;
        $UserProjet->user_id= $user_id;
        $UserProjet->save();

        
    }
}
