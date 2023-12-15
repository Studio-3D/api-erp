<?php

namespace App\Http\Helpers;

use App\Models\FreinOrientation;
use App\Models\Frein;

class FreinOrientationHelper
{
    public static function createFreinOrientation($orientation,$frein_id){
        $freinOrientation=new FreinOrientation();
        $freinOrientation->setConnection('temp');
        $freinOrientation->orientation=$orientation;
        $freinOrientation->frein_id=$frein_id;
        $freinOrientation->save();
    }
    public static function destroyFreinOrientation($frein_id){
        $freinOrientation=FreinOrientation::on('temp')->where('frein_id',$frein_id)->get();
       // $frein=Frein::on('temp')->findOrfail($frein_id);
        if(count($freinOrientation)>0){
                foreach($freinOrientation as $fr){
                    $fr->delete();
                }
              //  $frein->orientation=0;
              //  $frein->save();
        }

    }
}
