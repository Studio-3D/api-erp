<?php

namespace  App\Http\Helpers;

use App\Http\Requests\StoreTypeBienRequest;
use app\Http\Controllers\TypeBienController;


class ajouterTypeBienHelper
{
    public static function AjouterTypeBien($request, $projet_id)
    {
        if($request->donneesTypeBien){
            $typeBienController = new TypeBienController();
            $typeBienRequest = new StoreTypeBienRequest;
            foreach ($request->donneesTypeBien as $typeBiens) {
                $dataTypebien = [
                    'type' => $typeBiens,
                    'projet_id' => $projet_id,
                ];
                $typeBienRequest->merge($dataTypebien);
                $typeBienController->store($typeBienRequest);
            }
        }
       
    }
}

