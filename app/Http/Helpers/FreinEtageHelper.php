<?php

namespace App\Http\Helpers;

use App\Models\FreinEtage;
use App\Models\Frein;

class FreinEtageHelper
{
    public static function createFreinEtage($etage,$frein_id){
        $freinEtage=new FreinEtage();
        $freinEtage->setConnection('temp');
        $freinEtage->etage=$etage;
        $freinEtage->frein_id=$frein_id;
        $freinEtage->save();
    }
    public static function destroyFreinEtage($frein_id){
        $freinEtage=FreinEtage::on('temp')->where('frein_id',$frein_id)->get();
        $frein=Frein::on('temp')->findOrfail($frein_id);
        if(count($freinEtage)>0){
                foreach($freinEtage as $fr){
                    $fr->delete();
                }
                $frein->etage=0;
                $frein->save();
        }

    }
}
