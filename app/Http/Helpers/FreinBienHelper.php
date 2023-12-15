<?php

namespace App\Http\Helpers;

use App\Models\Frein_Bien;
use App\Models\Frein;
use App\Models\Notification;
use App\Http\Helpers\NotificationHelper;
use Carbon\Carbon;

class FreinBienHelper
{
    public static function createFreinBien($bien_id,$frein_id){
        $frein_bien=new Frein_Bien();
        $frein_bien->setConnection('temp');
        $frein_bien->bien_id=$bien_id;
        $frein_bien->frein_id=$frein_id;
        if($frein_bien->save()){
            $frein=Frein::on('temp')->findOrFail($frein_bien->frein_id);
            $frein->etat=2;//exit bien disponible convenable a ce frein
            if($frein->save()){
                //store notification bien disponible
                $notifications_bien_count=Notification::on('temp')->where('visite_id',$frein->visite_id)->where('type',3)->count();
                if($notifications_bien_count==0){
                NotificationHelper::storeNotification(
                    '/relances/visites/freins', Carbon::now(),3,'Bien Disponible Frein',$frein->visite->user->user_id_origin,$frein->visite_id,$frein->visite->prospect_id,$frein->visite->projet_id
                );

                }
            }

        }
    }

    public static function destroyFreinBien($frein_id){
        $frein_bien=Frein_Bien::on('temp')->where('frein_id',$frein_id)->get();
        if(count($frein_bien)>0){
                foreach($frein_bien as $fr){
                    $fr->forceDelete();
                }
        }
    }
}
