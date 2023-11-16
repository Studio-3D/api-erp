<?php

namespace App\Http\Helpers;

use App\Models\FreinTranche;
use App\Models\Frein;

class FreinTrancheHelper
{
    public static function createFreinTranche($tranche_id,$frein_id){
        $freinTranche=new FreinTranche();
        $freinTranche->setConnection('temp');
        $freinTranche->tranche_id=$tranche_id;
        $freinTranche->frein_id=$frein_id;
        $freinTranche->save();
    }

    public static function destroyFreinTranche($frein_id){
        $freinTranche=FreinTranche::on('temp')->where('frein_id',$frein_id)->get();
        $frein=Frein::on('temp')->findOrfail($frein_id);
        if(count($freinTranche)>0){
                foreach($freinTranche as $fr){
                    $fr->delete();
                }
                $frein->tranche=0;
                $frein->save();
        }

    }
}
