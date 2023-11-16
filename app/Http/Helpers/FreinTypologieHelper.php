<?php

namespace App\Http\Helpers;

use App\Models\FreinTypologie;
use App\Models\Frein;

class FreinTypologieHelper
{
    public static function createFreinTypologie($typologie,$frein_id){
        $freinTypologie=new FreinTypologie();
        $freinTypologie->setConnection('temp');
        $freinTypologie->typologie_id=$typologie;
        $freinTypologie->frein_id=$frein_id;
        $freinTypologie->save();
    }
    public static function destroyFreinTypologie($frein_id){
        $freinTypologie=FreinTypologie::on('temp')->where('frein_id',$frein_id)->get();
        $frein=Frein::on('temp')->findOrfail($frein_id);
        if(count($freinTypologie)>0){
                foreach($freinTypologie as $fr){
                    $fr->delete();
                }
                $frein->typologie=0;
                $frein->save();
        }
    }
}
