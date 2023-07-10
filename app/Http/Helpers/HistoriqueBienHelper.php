<?php

namespace  App\Http\Helpers;

use Illuminate\Support\Facades\Auth;
use App\Models\HistoriqueBien;
use App\Http\Helpers\DatabaseHelper;


class HistoriqueBienHelper
{
    public static function createHistoriqueBien($action, $description, $bienId, $user_id)
    {
        $historiqueBienData = [
            'action' => $action,
            'description' => $description,
            'user_id' => $user_id,
            'bien_id' => $bienId,
        ];

        $historiqueBien = HistoriqueBien::on('temp')->create($historiqueBienData);
    }
}
