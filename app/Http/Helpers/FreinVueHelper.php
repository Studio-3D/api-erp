<?php

namespace App\Http\Helpers;

use App\Models\FreinVue;
use App\Models\Frein;

class FreinVueHelper
{
    public static function createFreinVue($vue_id,$frein_id){
        $freinVue=new FreinVue();
        $freinVue->setConnection('temp');
        $freinVue->vue_id=$vue_id;
        $freinVue->frein_id=$frein_id;
        $freinVue->save();
    }

    public static function destroyFreinVue($frein_id){
        $freinVue=FreinVue::on('temp')->where('frein_id',$frein_id)->get();
       // $frein=Frein::on('temp')->findOrfail($frein_id);
        if(count($freinVue)>0){
                foreach($freinVue as $fr){
                    $fr->delete();
                }
               // $frein->vue=0;
               // $frein->save();
        }
    }
}
