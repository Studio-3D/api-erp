<?php

namespace  App\Http\Helpers;
use App\Http\Helpers\DatabaseHelper;
use App\Http\Helpers\HistoriqueBienHelper;
use Illuminate\Support\Facades\Auth;
use App\Models\Bien;
use App\Models\Frein_Bien;
use App\Models\Frein;
use App\Models\Notification;
use App\Models\Relance_Rdv_visite;
use App\Enum\EtatBien;
use App\Models\Visite;
use App\Enum\StatutVisiteEnum;
use App\Enum\InteretEnum;
use App\Models\User;
use Carbon\Carbon;
use App\Models\TypeBien;
use App\Models\CompositionBien; 
use Illuminate\Support\Facades\Log;






class Bien_Helper
{


public static function checkAndCreateBien($tranches, $projet_id, $blocs, $immeubles, $column)
{
    // if($tranches==null && $blocs==null && $immeubles==null )
    // {
    //    $bien_exist = Bien::on('temp')->where(function ($query) use ($column) {
    //     if (!empty($column['Appt_Num'])) {
    //         log::info('appt num not empty');
    //         $query->where('propriete_dite_bien', $column['Appt_Num']);
    //     } elseif (!empty($column['magasin_num'])) {
    //         log::info('magasin num not empty');
    //         $query->where('propriete_dite_bien', $column['magasin_num']);
    //     }
    //   })->where('projet_id', $projet_id)
        
    //     ->count();
    // }
    
    // elseif($tranches==null)
    // {
    //     $bien_exist = Bien::on('temp')->where(function ($query) use ($column) {
    //         if (!empty($column['Appt_Num'])) {
    //             log::info('appt num not empty');
    //             $query->where('propriete_dite_bien', $column['Appt_Num']);
    //         } elseif (!empty($column['magasin_num'])) {
    //             log::info('magasin num not empty');
    //             $query->where('propriete_dite_bien', $column['magasin_num']);
    //         }
    //       })
    //         ->where('projet_id', $projet_id)
    //         ->where('bloc_id', $blocs->id)
    //         ->count();
    //   }
    //   elseif($blocs==null)
    //   {
    //     $bien_exist = Bien::on('temp')->where(function ($query) use ($column) {
    //         if (!empty($column['Appt_Num'])) {
    //             log::info('appt num not empty');
    //             $query->where('propriete_dite_bien', $column['Appt_Num']);
    //         } elseif (!empty($column['magasin_num'])) {
    //             log::info('magasin num not empty');
    //             $query->where('propriete_dite_bien', $column['magasin_num']);
    //         }
    //       })
    //         ->where('projet_id', $projet_id)
    //         ->where('bloc_id', $blocs->id)
    //         ->count();
    //   }
    //   elseif($tranches==null)
    //   {
    //     $bien_exist = Bien::on('temp')->where(function ($query) use ($column) {
    //         if (!empty($column['Appt_Num'])) {
    //             log::info('appt num not empty');
    //             $query->where('propriete_dite_bien', $column['Appt_Num']);
    //         } elseif (!empty($column['magasin_num'])) {
    //             log::info('magasin num not empty');
    //             $query->where('propriete_dite_bien', $column['magasin_num']);
    //         }
    //       })
    //         ->where('projet_id', $projet_id)
    //         ->where('bloc_id', $blocs->id)
    //         ->count();
    //   }
    //   elseif($tranches==null && $blocs==null){
    //   $bien_exist = Bien::on('temp')->where(function ($query) use ($column) {
    //     if (!empty($column['Appt_Num'])) {
    //         log::info('appt num not empty');
    //         $query->where('propriete_dite_bien', $column['Appt_Num']);
    //     } elseif (!empty($column['magasin_num'])) {
    //         log::info('magasin num not empty');
    //         $query->where('propriete_dite_bien', $column['magasin_num']);
    //     }
    //   })
    //     ->where('projet_id', $projet_id)
    //     ->where('immeuble_id', $immeubles->id)
    //     ->count();
     

    //   }
    //   elseif($tranches==null&& $immeubles==null)
    //   {
    //     $bien_exist = Bien::on('temp')->where(function ($query) use ($column) {
    //         if (!empty($column['Appt_Num'])) {
    //             log::info('appt num not empty');
    //             $query->where('propriete_dite_bien', $column['Appt_Num']);
    //         } elseif (!empty($column['magasin_num'])) {
    //             log::info('magasin num not empty');
    //             $query->where('propriete_dite_bien', $column['magasin_num']);
    //         }
    //       })
    //         ->where('projet_id', $projet_id)
    //         ->where('bloc_id', $blocs->id)
    //         ->count();

    //   }
    //   elseif($blocs==null&&$immeubles==null)
    //   {
    //    $bien_exist = Bien::on('temp')->where(function ($query) use ($column) {
    //         if (!empty($column['Appt_Num'])) {
    //             log::info('appt num not empty');
    //             $query->where('propriete_dite_bien', $column['Appt_Num']);
    //         } elseif (!empty($column['magasin_num'])) {
    //             log::info('magasin num not empty');
    //             $query->where('propriete_dite_bien', $column['magasin_num']);
    //         }
    //       })
    //         ->where('projet_id', $projet_id)
    //         ->where('tranche_id', $tranches->id)
    //         ->count();
    //   }
    //   else{
    //     $bien_exist = Bien::on('temp')->where(function ($query) use ($column) {
    //         if (!empty($column['Appt_Num'])) {
    //             log::info('appt num not empty');
    //             $query->where('propriete_dite_bien', $column['Appt_Num']);
    //         } elseif (!empty($column['magasin_num'])) {
    //             log::info('magasin num not empty');
    //             $query->where('propriete_dite_bien', $column['magasin_num']);
    //         }
    //       })
    //         ->where('projet_id', $projet_id)
    //         ->where('tranche_id', $tranches->id)
    //         ->where('bloc_id', $blocs->id)
    //         ->where('immeuble_id', $immeubles->id)
    //         ->count();

    //   }
    $bien_exist = Bien::on('temp')->where(function ($query) use ($column, $projet_id, $tranches, $blocs, $immeubles) {
        if (!empty($column['Appt_Num'])) {
            log::info('appt num not empty');
            $query->where('propriete_dite_bien', $column['Appt_Num']);
        } elseif (!empty($column['magasin_num'])) {
            log::info('magasin num not empty');
            $query->where('propriete_dite_bien', $column['magasin_num']);
        }
    
        if ($tranches !== null) {
            $query->where('tranche_id', $tranches->id);
            log::info('tranche null');
        }
    
        if ($blocs !== null) {
            $query->where('bloc_id', $blocs->id);
            log::info('bloc null');
        }
    
        if ($immeubles !== null) {
            $query->where('immeuble_id', $immeubles->id);
            log::info('immeu null');
        }
    
        $query->where('projet_id', $projet_id);
    })->count();
    

   
    log::info('uts done here 1');
    if ($bien_exist == 0) {
        log::info('uts done here 2');

        $bien= new  Bien();
        $bien->setConnection('temp');
        $bien->bloc_id=$blocs->id ?? null;
        $bien->immeuble_id = $immeubles->id ?? null;
        
        // Log::info($immeubles->id);
        // Log::info($blocs->id);

        

        if (array_key_exists("Appt_Num",$column) && $column['Appt_Num']!=null ){
            log::info('uts done here 3');
                $explode_numero = explode("Appt", $column['Appt_Num']);
                $bien->numero=$explode_numero[1];
                $bien->propriete_dite_bien=$column['Appt_Num'];
           

        }
        if (array_key_exists("magasin_num",$column) && $column['magasin_num']!=null){
           
                 $bien->numero=$column['magasin_num'];
                 $bien->propriete_dite_bien=$column['magasin_num'];
        }

            log::info('its done here 4');

        if (array_key_exists("Niveau",$column) && array_key_exists("etage",$column)){

            if($column['Niveau']!=null){


                 if (str_contains($column['Niveau'], 'er etage')) {
                      $explode_Niveau_1 = explode("er etage", $column['Niveau']);
                      $bien->Niveau=$explode_Niveau_1[0];
                      $nv=$explode_Niveau_1[0];
                 }elseif(str_contains($column['Niveau'], 'eme etage')){
                      $explode_Niveau_2 = explode("eme etage", $column['Niveau']);
                      $bien->Niveau=$explode_Niveau_2[0];
                      $nv=$explode_Niveau_2[0];

                 }elseif(str_contains($column['Niveau'], 'ème etage')){
                      $explode_Niveau_3 = explode("ème etage", $column['Niveau']);
                      $bien->Niveau=$explode_Niveau_3[0];
                      $nv=$explode_Niveau_2[0];
                 }
                 elseif(str_contains($column['Niveau'], 'RDC')){
                    $bien->Niveau=0;
                    $nv=0;
                 }


            }elseif($column['etage']!=null){
                $bien->Niveau=$column['etage'];
                 $nv=$column['etage'];

            }
        }else{
            if($column['Niveau']!=null){
                if (str_contains($column['Niveau'], 'er etage')) {
                         $explode_Niveau_1 = explode("er etage", $column['Niveau']);
                         $bien->Niveau=$explode_Niveau_1[0];
                         $nv=$explode_Niveau_1[0];
                    }elseif(str_contains($column['Niveau'], 'eme etage')){
                         $explode_Niveau_2 = explode("eme etage", $column['Niveau']);
                         $bien->Niveau=$explode_Niveau_2[0];
                         $nv=$explode_Niveau_2[0];

                    }elseif(str_contains($column['Niveau'], 'ème etage')){
                         $explode_Niveau_3 = explode("ème etage", $column['Niveau']);
                         $bien->Niveau=$explode_Niveau_3[0];
                         $nv=$explode_Niveau_2[0];
                    }
                    elseif(str_contains($column['Niveau'], 'RDC')){
                       $bien->Niveau=0;
                       $nv=0;
                    }
           }

        }

        log::info('uts done here 5');

        if ($column['type_local']=='APPARTEMENT'){
            $type=TypeBien::on('temp')->where('type','Appartement')->get()->first();
            $bien->type_id=$type->id;

        }

        elseif ($column['type_local']=='LOCAL COMMERCIAL') {
            $type=TypeBien::on('temp')->where('type','Magasin')->get()->first();
            $bien->type_id=$type->id;

        }

        log::info('uts done here 7');
        log::info($type->id);


        // $bien->partie_p = $column['partie_p'];

        if (array_key_exists("parking",$column)){
            if ($column['parking'] == NULL) {

                 $bien->prix_parking = 0;
            } else {
                 $bien->prix_parking = $column['parking'];
            }
        }

        else{
            $bien->superficie_balcon = 0;
       }

       if (array_key_exists("balcon",$column)){
        if ($column['balcon'] == NULL || $column['balcon'] == 'SYNDIC PROPOSE'||$column['balcon']=='SYNDIC PLAN') {
            $sup_balcon=0;
             $bien->superficie_balcon = 0;

        } else {
            $bien->superficie_balcon = $column['balcon'];

        }
        
      }else{
        $bien->superficie_balcon = 0;

      }
      if (array_key_exists("terrasse",$column)){
        if ($column['terrasse'] == NULL) {
            log::info('uts done here 6');

            $bien->superficie_terrasse = 0;
            

        } else {

            $bien->superficie_terrasse = $column['terrasse'];

        }
    }else{

        $bien->superficie_terrasse = 0;

    }
    $bien->superficie_terrasse_calculer=$bien->superficie_terrasse;
    $bien-> superficie_balcon_calculer=$bien->superficie_balcon;

    if (array_key_exists("superficie_architect",$column)){
        if ($column['superficie_architect'] == NULL) {
            $bien-> superficie_architecte =0;

        } else {
            $bien->superficie_architecte =$column['superficie_architect']-$bien->superficie_terrasse_calculer-$bien-> superficie_balcon_calculer;

        }
    }else{

        $bien->superficie_architecte = 0;

    }
    $bien->superficie_architecte = 0;

    if (array_key_exists("pu",$column)){
        if ($column['pu'] == NULL) {
        
            if($nv==0){
                $bien->prix_unitaire=11500;
            }else{
            
                $bien->prix_unitaire=12000;
            }

        }
        else{
            $bien->prix_unitaire=$column['pu'];
        }
    }else{
        if($column['type_local']=='LOCAL COMMERCIAL'){
            $bien->prix_unitaire=25000;

        }else{
            $bien->prix_unitaire=0;
        }
    }
    if (array_key_exists("prix_box",$column)){
        if ($column['prix_box'] == NULL) {
            $bien->prix_box = 0;

        } else {
            $bien->prix_box = $column['prix_box'];

        }
    }else{
         $bien->prix_box = 0;

    }

                        $sup=$bien->superficie;
                        $bien->superficie_total=$sup+$bien->superficie_balcon+$bien->superficie_terrasse;
                        $bien->prix=$bien->prix_unitaire*($sup)+$bien->prix_parking+ $bien->prix_box;
                        $bien->etat='disponible';
                        $bien->orientation = 'N';
                        $bien->conventionne = 0;
                        // $bien->tranche_id = $tranches->id;
                        $bien->tranche_id = $tranches->id ?? null;

                        $bien->projet_id = $projet_id;
                        $bien->avance_minimale = 0;
                        $bien->nbre_facades=0;
                        $bien->superficie_vendable=0;

                        
                        Log::info('bien  before save');
                        
                        if($bien->save()){
                            Log::info('bien   save  Succ');



                            if (array_key_exists("Categorie",$column)){
                                log::info('category here');
                                $pattern = "/[,\s.]/";
                                $exp=preg_split($pattern, $column['Categorie']);

                                $balcon=0;
                                $chambre=0;
                                $salon=0;
                                $cuisin=0;
                                $sdb=0;
                                $placard=0;
                                $terasse=0;
                                for($i=0;$i<=count($exp)-1;$i++){

                                    if (str_contains($exp[$i], 'CHAMBRES')) {
                                         $chambre=explode("CHAMBRES",$exp[$i]);
                                        $chambre=$chambre[0];
                                    }elseif (str_contains($exp[$i], 'PLACARDS')) {
                                         $placard=explode("PLACARDS",$exp[$i]);
                                        $placard=$placard[0];
                                    }
                                    elseif (str_contains($exp[$i], 'SALON')) {
                                         $salon=explode("SALON",$exp[$i]);
                                         $salon=$salon[0];
                                    }
                                    elseif (str_contains($exp[$i], ' ')) {
                                         $cuisin=explode("CUISINE",$exp[$i]);
                                         $cuisin=$cuisin[0];
                                    }
                                    elseif (str_contains($exp[$i], 'SDB')) {
                                         $sdb=explode("SDB",$exp[$i]);
                                        $sdb=$sdb[0];
                                    }
                                     elseif (str_contains($exp[$i], 'PLACARDS')) {
                                         $placard=explode("PLACARDS",$exp[$i]);
                                        $placard=$placard[0];
                                    }
                                     elseif (str_contains($exp[$i], 'TERRASSE')) {
                                         $terassse=explode("TERRASSE",$exp[$i]);
                                        $terassse=$terassse[0];
                                    }
                                    elseif (str_contains($exp[$i], 'BALCON')) {
                                         $balcon=explode("BALCON",$exp[$i]);
                                        $balcon=$balcon[0];
                                    }

                                }
                                $compo=new CompositionBien();
                                $compo->setConnection('temp');
                                $compo->bien_id=$bien->id;
                                $compo->nbre_chambres=$chambre;
                                $compo->nbre_salons=$salon;
                                $compo->nbre_sdb=$sdb;
                                $compo->nbre_cuisines=$cuisin;
                                $compo->nbre_balcons=$balcon;
                                $compo->nbre_terasses=$terasse;
                                $compo->nbre_placards=$placard;
                                $compo->save();

                            }

                        }    }
    // log::info('bien exist');
}

public static function checkAndCreateBien2( $projet_id,$tranches,$blocs, $immeubles, $column)
{
    $bien_exist = Bien::on('temp')->where(function ($query) use ($column) {
        if (!empty($column['Appt_Num'])) {
            log::info('appt num not empty');
            $query->where('propriete_dite_bien', $column['Appt_Num']);
        } elseif (!empty($column['magasin_num'])) {
            log::info('magasin num not empty');
            $query->where('propriete_dite_bien', $column['magasin_num']);
        }
    })
        ->where('projet_id', $projet_id)
        ->where('bloc_id', $blocs->id)
       
        ->count();

    log::info('uts done here 1');
    if ($bien_exist == 0) {
        log::info('uts done here 2');

        $bien= new  Bien();
        $bien->setConnection('temp');
        $bien->bloc_id=$blocs->id;
        $bien->immeuble_id = $immeubles->id;
        
        Log::info($immeubles->id);
        Log::info($blocs->id);

        

        if (array_key_exists("Appt_Num",$column) && $column['Appt_Num']!=null ){
            log::info('uts done here 3');
                $explode_numero = explode("Appt", $column['Appt_Num']);
                $bien->numero=$explode_numero[1];
                $bien->propriete_dite_bien=$column['Appt_Num'];
           

        }
        if (array_key_exists("magasin_num",$column) && $column['magasin_num']!=null){
           
                 $bien->numero=$column['magasin_num'];
                 $bien->propriete_dite_bien=$column['magasin_num'];
        }

            log::info('its done here 4');

        if (array_key_exists("Niveau",$column) && array_key_exists("etage",$column)){

            if($column['Niveau']!=null){


                 if (str_contains($column['Niveau'], 'er etage')) {
                      $explode_Niveau_1 = explode("er etage", $column['Niveau']);
                      $bien->Niveau=$explode_Niveau_1[0];
                      $nv=$explode_Niveau_1[0];
                 }elseif(str_contains($column['Niveau'], 'eme etage')){
                      $explode_Niveau_2 = explode("eme etage", $column['Niveau']);
                      $bien->Niveau=$explode_Niveau_2[0];
                      $nv=$explode_Niveau_2[0];

                 }elseif(str_contains($column['Niveau'], 'ème etage')){
                      $explode_Niveau_3 = explode("ème etage", $column['Niveau']);
                      $bien->Niveau=$explode_Niveau_3[0];
                      $nv=$explode_Niveau_2[0];
                 }
                 elseif(str_contains($column['Niveau'], 'RDC')){
                    $bien->Niveau=0;
                    $nv=0;
                 }


            }elseif($column['etage']!=null){
                $bien->Niveau=$column['etage'];
                 $nv=$column['etage'];

            }
        }else{
            if($column['Niveau']!=null){
                if (str_contains($column['Niveau'], 'er etage')) {
                         $explode_Niveau_1 = explode("er etage", $column['Niveau']);
                         $bien->Niveau=$explode_Niveau_1[0];
                         $nv=$explode_Niveau_1[0];
                    }elseif(str_contains($column['Niveau'], 'eme etage')){
                         $explode_Niveau_2 = explode("eme etage", $column['Niveau']);
                         $bien->Niveau=$explode_Niveau_2[0];
                         $nv=$explode_Niveau_2[0];

                    }elseif(str_contains($column['Niveau'], 'ème etage')){
                         $explode_Niveau_3 = explode("ème etage", $column['Niveau']);
                         $bien->Niveau=$explode_Niveau_3[0];
                         $nv=$explode_Niveau_2[0];
                    }
                    elseif(str_contains($column['Niveau'], 'RDC')){
                       $bien->Niveau=0;
                       $nv=0;
                    }
           }

        }

        log::info('uts done here 5');

        if ($column['type_local']=='APPARTEMENT'){
            $type=TypeBien::on('temp')->where('type','Appartement')->get()->first();
            $bien->type_id=$type->id;

        }

        elseif ($column['type_local']=='LOCAL COMMERCIAL') {
            $type=TypeBien::on('temp')->where('type','Magasin')->get()->first();
            $bien->type_id=$type->id;

        }

        log::info('uts done here 7');
        log::info($type->id);


        // $bien->partie_p = $column['partie_p'];

        if (array_key_exists("parking",$column)){
            if ($column['parking'] == NULL) {

                 $bien->prix_parking = 0;
            } else {
                 $bien->prix_parking = $column['parking'];
            }
        }

        else{
            $bien->superficie_balcon = 0;
       }

       if (array_key_exists("balcon",$column)){
        if ($column['balcon'] == NULL || $column['balcon'] == 'SYNDIC PROPOSE'||$column['balcon']=='SYNDIC PLAN') {
            $sup_balcon=0;
             $bien->superficie_balcon = 0;

        } else {
            $bien->superficie_balcon = $column['balcon'];

        }
        
      }else{
        $bien->superficie_balcon = 0;

      }
      if (array_key_exists("terrasse",$column)){
        if ($column['terrasse'] == NULL) {
            log::info('uts done here 6');

            $bien->superficie_terrasse = 0;
            

        } else {

            $bien->superficie_terrasse = $column['terrasse'];

        }
    }else{

        $bien->superficie_terrasse = 0;

    }
    $bien->superficie_terrasse_calculer=$bien->superficie_terrasse;
    $bien-> superficie_balcon_calculer=$bien->superficie_balcon;

    if (array_key_exists("superficie_architect",$column)){
        if ($column['superficie_architect'] == NULL) {
            $bien-> superficie_architecte =0;

        } else {
            $bien->superficie_architecte =$column['superficie_architect']-$bien->superficie_terrasse_calculer-$bien-> superficie_balcon_calculer;

        }
    }else{

        $bien->superficie_architecte = 0;

    }
    $bien->superficie_architecte = 0;

    if (array_key_exists("pu",$column)){
        if ($column['pu'] == NULL) {
        
            if($nv==0){
                $bien->prix_unitaire=11500;
            }else{
            
                $bien->prix_unitaire=12000;
            }

        }
        else{
            $bien->prix_unitaire=$column['pu'];
        }
    }else{
        if($column['type_local']=='LOCAL COMMERCIAL'){
            $bien->prix_unitaire=25000;

        }else{
            $bien->prix_unitaire=0;
        }
    }
    if (array_key_exists("prix_box",$column)){
        if ($column['prix_box'] == NULL) {
            $bien->prix_box = 0;

        } else {
            $bien->prix_box = $column['prix_box'];

        }
    }else{
         $bien->prix_box = 0;

    }

                        $sup=$bien->superficie;
                        $bien->superficie_total=$sup+$bien->superficie_balcon+$bien->superficie_terrasse;
                        $bien->prix=$bien->prix_unitaire*($sup)+$bien->prix_parking+ $bien->prix_box;
                        $bien->etat='disponible';
                        $bien->orientation = 'N';
                        $bien->conventionne = 0;
                        $bien->projet_id = $projet_id;
                        $bien->avance_minimale = 0;
                        $bien->nbre_facades=0;
                        $bien->superficie_vendable=0;

                        
                        Log::info('bien  before save');
                        
                        if($bien->save()){
                            Log::info('bien   save  Succ');



                            if (array_key_exists("Categorie",$column)){
                                log::info('category here');
                                $pattern = "/[,\s.]/";
                                $exp=preg_split($pattern, $column['Categorie']);

                                $balcon=0;
                                $chambre=0;
                                $salon=0;
                                $cuisin=0;
                                $sdb=0;
                                $placard=0;
                                $terasse=0;
                                for($i=0;$i<=count($exp)-1;$i++){

                                    if (str_contains($exp[$i], 'CHAMBRES')) {
                                         $chambre=explode("CHAMBRES",$exp[$i]);
                                        $chambre=$chambre[0];
                                    }elseif (str_contains($exp[$i], 'PLACARDS')) {
                                         $placard=explode("PLACARDS",$exp[$i]);
                                        $placard=$placard[0];
                                    }
                                    elseif (str_contains($exp[$i], 'SALON')) {
                                         $salon=explode("SALON",$exp[$i]);
                                         $salon=$salon[0];
                                    }
                                    elseif (str_contains($exp[$i], ' ')) {
                                         $cuisin=explode("CUISINE",$exp[$i]);
                                         $cuisin=$cuisin[0];
                                    }
                                    elseif (str_contains($exp[$i], 'SDB')) {
                                         $sdb=explode("SDB",$exp[$i]);
                                        $sdb=$sdb[0];
                                    }
                                     elseif (str_contains($exp[$i], 'PLACARDS')) {
                                         $placard=explode("PLACARDS",$exp[$i]);
                                        $placard=$placard[0];
                                    }
                                     elseif (str_contains($exp[$i], 'TERRASSE')) {
                                         $terassse=explode("TERRASSE",$exp[$i]);
                                        $terassse=$terassse[0];
                                    }
                                    elseif (str_contains($exp[$i], 'BALCON')) {
                                         $balcon=explode("BALCON",$exp[$i]);
                                        $balcon=$balcon[0];
                                    }

                                }
                                $compo=new CompositionBien();
                                $compo->setConnection('temp');
                                $compo->bien_id=$bien->id;
                                $compo->nbre_chambres=$chambre;
                                $compo->nbre_salons=$salon;
                                $compo->nbre_sdb=$sdb;
                                $compo->nbre_cuisines=$cuisin;
                                $compo->nbre_balcons=$balcon;
                                $compo->nbre_terasses=$terasse;
                                $compo->nbre_placards=$placard;
                                $compo->save();

                                
                                Log::info('bien save succ with out tranche');
                                

                            }

                        }    }
    // log::info('bien exist');
}



public static function checkAndCreateBien3( $projet_id, $immeubles, $column)
{
    $bien_exist = Bien::on('temp')->where(function ($query) use ($column) {
        if (!empty($column['Appt_Num'])) {
            log::info('appt num not empty');
            $query->where('propriete_dite_bien', $column['Appt_Num']);
        } elseif (!empty($column['magasin_num'])) {
            log::info('magasin num not empty');
            $query->where('propriete_dite_bien', $column['magasin_num']);
        }
    })
        ->where('projet_id', $projet_id)
        // ->where('bloc_id', $blocs->id)
        ->count();

    log::info('uts done here 1');
    if ($bien_exist == 0) {
        log::info('uts done here 2');

        $bien= new  Bien();
        $bien->setConnection('temp');
        // $bien->bloc_id=$blocs->id;
        $bien->immeuble_id = $immeubles->id;
        
        Log::info($immeubles->id);
        Log::info($blocs->id);

        

        if (array_key_exists("Appt_Num",$column) && $column['Appt_Num']!=null ){
            log::info('uts done here 3');
                $explode_numero = explode("Appt", $column['Appt_Num']);
                $bien->numero=$explode_numero[1];
                $bien->propriete_dite_bien=$column['Appt_Num'];
           

        }
        if (array_key_exists("magasin_num",$column) && $column['magasin_num']!=null){
           
                 $bien->numero=$column['magasin_num'];
                 $bien->propriete_dite_bien=$column['magasin_num'];
        }

            log::info('its done here 4');

        if (array_key_exists("Niveau",$column) && array_key_exists("etage",$column)){

            if($column['Niveau']!=null){


                 if (str_contains($column['Niveau'], 'er etage')) {
                      $explode_Niveau_1 = explode("er etage", $column['Niveau']);
                      $bien->Niveau=$explode_Niveau_1[0];
                      $nv=$explode_Niveau_1[0];
                 }elseif(str_contains($column['Niveau'], 'eme etage')){
                      $explode_Niveau_2 = explode("eme etage", $column['Niveau']);
                      $bien->Niveau=$explode_Niveau_2[0];
                      $nv=$explode_Niveau_2[0];

                 }elseif(str_contains($column['Niveau'], 'ème etage')){
                      $explode_Niveau_3 = explode("ème etage", $column['Niveau']);
                      $bien->Niveau=$explode_Niveau_3[0];
                      $nv=$explode_Niveau_2[0];
                 }
                 elseif(str_contains($column['Niveau'], 'RDC')){
                    $bien->Niveau=0;
                    $nv=0;
                 }


            }elseif($column['etage']!=null){
                $bien->Niveau=$column['etage'];
                 $nv=$column['etage'];

            }
        }else{
            if($column['Niveau']!=null){
                if (str_contains($column['Niveau'], 'er etage')) {
                         $explode_Niveau_1 = explode("er etage", $column['Niveau']);
                         $bien->Niveau=$explode_Niveau_1[0];
                         $nv=$explode_Niveau_1[0];
                    }elseif(str_contains($column['Niveau'], 'eme etage')){
                         $explode_Niveau_2 = explode("eme etage", $column['Niveau']);
                         $bien->Niveau=$explode_Niveau_2[0];
                         $nv=$explode_Niveau_2[0];

                    }elseif(str_contains($column['Niveau'], 'ème etage')){
                         $explode_Niveau_3 = explode("ème etage", $column['Niveau']);
                         $bien->Niveau=$explode_Niveau_3[0];
                         $nv=$explode_Niveau_2[0];
                    }
                    elseif(str_contains($column['Niveau'], 'RDC')){
                       $bien->Niveau=0;
                       $nv=0;
                    }
           }

        }

        log::info('uts done here 5');

        if ($column['type_local']=='APPARTEMENT'){
            $type=TypeBien::on('temp')->where('type','Appartement')->get()->first();
            $bien->type_id=$type->id;

        }

        elseif ($column['type_local']=='LOCAL COMMERCIAL') {
            $type=TypeBien::on('temp')->where('type','Magasin')->get()->first();
            $bien->type_id=$type->id;

        }

        log::info('uts done here 7');
        log::info($type->id);


        // $bien->partie_p = $column['partie_p'];

        if (array_key_exists("parking",$column)){
            if ($column['parking'] == NULL) {

                 $bien->prix_parking = 0;
            } else {
                 $bien->prix_parking = $column['parking'];
            }
        }

        else{
            $bien->superficie_balcon = 0;
       }

       if (array_key_exists("balcon",$column)){
        if ($column['balcon'] == NULL || $column['balcon'] == 'SYNDIC PROPOSE'||$column['balcon']=='SYNDIC PLAN') {
            $sup_balcon=0;
             $bien->superficie_balcon = 0;

        } else {
            $bien->superficie_balcon = $column['balcon'];

        }
        
      }else{
        $bien->superficie_balcon = 0;

      }
      if (array_key_exists("terrasse",$column)){
        if ($column['terrasse'] == NULL) {
            log::info('uts done here 6');

            $bien->superficie_terrasse = 0;
            

        } else {

            $bien->superficie_terrasse = $column['terrasse'];

        }
    }else{

        $bien->superficie_terrasse = 0;

    }
    $bien->superficie_terrasse_calculer=$bien->superficie_terrasse;
    $bien-> superficie_balcon_calculer=$bien->superficie_balcon;

    if (array_key_exists("superficie_architect",$column)){
        if ($column['superficie_architect'] == NULL) {
            $bien-> superficie_architecte =0;

        } else {
            $bien->superficie_architecte =$column['superficie_architect']-$bien->superficie_terrasse_calculer-$bien-> superficie_balcon_calculer;

        }
    }else{

        $bien->superficie_architecte = 0;

    }
    $bien->superficie_architecte = 0;

    if (array_key_exists("pu",$column)){
        if ($column['pu'] == NULL) {
        
            if($nv==0){
                $bien->prix_unitaire=11500;
            }else{
            
                $bien->prix_unitaire=12000;
            }

        }
        else{
            $bien->prix_unitaire=$column['pu'];
        }
    }else{
        if($column['type_local']=='LOCAL COMMERCIAL'){
            $bien->prix_unitaire=25000;

        }else{
            $bien->prix_unitaire=0;
        }
    }
    if (array_key_exists("prix_box",$column)){
        if ($column['prix_box'] == NULL) {
            $bien->prix_box = 0;

        } else {
            $bien->prix_box = $column['prix_box'];

        }
    }else{
         $bien->prix_box = 0;

    }

                        $sup=$bien->superficie;
                        $bien->superficie_total=$sup+$bien->superficie_balcon+$bien->superficie_terrasse;
                        $bien->prix=$bien->prix_unitaire*($sup)+$bien->prix_parking+ $bien->prix_box;
                        $bien->etat='disponible';
                        $bien->orientation = 'N';
                        $bien->conventionne = 0;
                        $bien->projet_id = $projet_id;
                        $bien->avance_minimale = 0;
                        $bien->nbre_facades=0;
                        $bien->superficie_vendable=0;

                        
                        Log::info('bien  before save');
                        
                        if($bien->save()){
                            Log::info('bien   save  Succ');



                            if (array_key_exists("Categorie",$column)){
                                log::info('category here');
                                $pattern = "/[,\s.]/";
                                $exp=preg_split($pattern, $column['Categorie']);

                                $balcon=0;
                                $chambre=0;
                                $salon=0;
                                $cuisin=0;
                                $sdb=0;
                                $placard=0;
                                $terasse=0;
                                for($i=0;$i<=count($exp)-1;$i++){

                                    if (str_contains($exp[$i], 'CHAMBRES')) {
                                         $chambre=explode("CHAMBRES",$exp[$i]);
                                        $chambre=$chambre[0];
                                    }elseif (str_contains($exp[$i], 'PLACARDS')) {
                                         $placard=explode("PLACARDS",$exp[$i]);
                                        $placard=$placard[0];
                                    }
                                    elseif (str_contains($exp[$i], 'SALON')) {
                                         $salon=explode("SALON",$exp[$i]);
                                         $salon=$salon[0];
                                    }
                                    elseif (str_contains($exp[$i], ' ')) {
                                         $cuisin=explode("CUISINE",$exp[$i]);
                                         $cuisin=$cuisin[0];
                                    }
                                    elseif (str_contains($exp[$i], 'SDB')) {
                                         $sdb=explode("SDB",$exp[$i]);
                                        $sdb=$sdb[0];
                                    }
                                     elseif (str_contains($exp[$i], 'PLACARDS')) {
                                         $placard=explode("PLACARDS",$exp[$i]);
                                        $placard=$placard[0];
                                    }
                                     elseif (str_contains($exp[$i], 'TERRASSE')) {
                                         $terassse=explode("TERRASSE",$exp[$i]);
                                        $terassse=$terassse[0];
                                    }
                                    elseif (str_contains($exp[$i], 'BALCON')) {
                                         $balcon=explode("BALCON",$exp[$i]);
                                        $balcon=$balcon[0];
                                    }

                                }
                                $compo=new CompositionBien();
                                $compo->setConnection('temp');
                                $compo->bien_id=$bien->id;
                                $compo->nbre_chambres=$chambre;
                                $compo->nbre_salons=$salon;
                                $compo->nbre_sdb=$sdb;
                                $compo->nbre_cuisines=$cuisin;
                                $compo->nbre_balcons=$balcon;
                                $compo->nbre_terasses=$terasse;
                                $compo->nbre_placards=$placard;
                                $compo->save();

                                
                                Log::info('bien save succ with out tranche');
                                

                            }

                        }    }
    // log::info('bien exist');
}


    public static function libererBien($id,$text,$dst_id)

    {
        $user = Auth::user();
        $userAuth = User::on('temp')->where('user_id_origin', $user->getAuthIdentifier())->get();
        $bien = Bien::on('temp')->findOrfail($id);
        $bien->etat = EtatBien::DISPONIBLE->value;
        $bien->desistement_id=$dst_id;
        if($bien->save()){
            Bien_Helper::store_bien_frein($bien->id);
            //UPDATE DERNIER VISITE pre reserve=>pre reserve_perdu // vendu==>reservation_perdu
            $visite=Visite::on('temp')->where('bien_id',$id)->where('interet',InteretEnum::Intéressé->value)->orderBy('created_at', 'DESC')->first();
            if($visite!=null){
                if($text=='console'){
                //pre reserve
                if($visite->statut==StatutVisiteEnum::Pré_Réservation->value){
                    $visite->statut=StatutVisiteEnum::Pré_Réservation_Perdu->value;
                }elseif($visite->statut==StatutVisiteEnum::Vendu->value){
                    $visite->statut=StatutVisiteEnum::Réservation_Perdu->value;
                }
                $visite->save();
                }

                //SUPPRIMER LES OLDS NOTIF
                $notif_old_relance=Notification::on('temp')->where(function ($query){
                    $query->where('type',1)
                        ->orwhere('type',2);})
                    ->where(function ($query_2) use($visite){
                            $query_2->where('visite_id',$visite->id);})
                    ->get();
                    if(($notif_old_relance->count())>0){
                       foreach($notif_old_relance as $nt_r){
                        $nt_r->delete();
                       }
                    }
                /***RENDRE LES OLD RELANCES ET OLD RDV EN TRAITE AUTOMATIQUE****/
                    $old_relances_rdv=Relance_Rdv_visite::on('temp')->where('visite_id',$visite->id)->where('type_traitement',0)->get();
                    if(count($old_relances_rdv)>0){
                        foreach($old_relances_rdv as $old){
                            $old->type_traitement=2;//auto
                            $old->date_traitement=Carbon::now();
                            //si old visite pre reserve en suite n visite vendu ==>user_id_traite(l'ancien user)
                            if($old->visite->statut==StatutVisiteEnum::Pré_Réservation->value){
                                if($visite->statut==StatutVisiteEnum::Vendu->value){
                                    $old->user_id_traite=$visite->user_id;
                                }
                                else{
                                    $old->user_id_traite=$userAuth->value('id');
                                }
                            }
                            else{
                                $old->user_id_traite=$userAuth->value('id');
                            }
                            $old->save();
                        }

                    }
            }
        }
        if($text=='console'){
            HistoriqueBienHelper::createHistoriqueBien(1, "liberation automatique",$id,NULL,NULL,NULL);
        }
        else{
            HistoriqueBienHelper::createHistoriqueBien(4, "liberer", $id, Auth::guard('api')->user()->id,NULL,NULL);

        }
    }
    public static function store_bien_frein($id)
    {

        $bien=Bien::on('temp')->findorfail($id);
        $array_fr_id=array();
        $freins= Frein::on('temp')
        ->join('visites', 'visites.id', '=', 'freins.visite_id')
        ->leftjoin('frein_tranches', 'frein_tranches.frein_id', '=', 'freins.id')
        ->leftjoin('frein_etages', 'frein_etages.frein_id', '=', 'freins.id')
        ->leftjoin('frein_orientations', 'frein_orientations.frein_id', '=', 'freins.id')
        ->leftjoin('frein_typologies', 'frein_typologies.frein_id', '=', 'freins.id')
        ->leftjoin('frein_vues', 'frein_vues.frein_id', '=', 'freins.id')
        ->select('freins.id','freins.tranche as fr_tranche','freins.etage as fr_etage',
        'freins.orientation as fr_orientation','freins.typologie as fr_typologie',
       'freins.vue as fr_vue','freins.prix_min as fr_prix_min','freins.prix_max as fr_prix_max',
        'freins.superficie_min as fr_superficie_min','freins.superficie_max as fr_superficie_max',
        'frein_tranches.tranche_id','frein_etages.etage',
        'frein_orientations.orientation','frein_typologies.typologie_id','frein_vues.vue_id','freins.avance as fr_avance'
        )
        ->where('visites.projet_id', $bien->projet_id)
        ->whereIN('freins.etat', [1,2])
        ->where('visites.etat', 1)
        ->get();

        foreach($freins as $fr){
            if( ($fr->fr_tranche==1 && $fr->tranche_id==$bien->tranche_id)
            && ($fr->fr_etage==1 && $fr->etage==$bien->niveau)
            && ($fr->fr_orientation==1 && $fr->orientation==$bien->orientation)
            && ($fr->fr_typologie==1 && $fr->typologie_id==$bien->typologie_id)
            && ($fr->fr_vue==1 && $fr->vue_id==$bien->vue_id)
            && ($fr->fr_prix_min!=null && $fr->fr_prix_min<=$bien->prix)
            && ($fr->fr_prix_max!=null && $fr->fr_prix_max>=$bien->prix)
            && ($fr->fr_superficie_min!=null && $fr->fr_superficie_min<=$bien->superficie_habitable)
            && ($fr->fr_superficie_max!=null && $fr->fr_superficie_max>=$bien->superficie_habitable)
            && (($fr->fr_avance!=null ||$fr->fr_avance!=0 ) && $fr->fr_avance<=$bien->avance_minimale)
            ){
                $exist=0;
                     //test si id du frein exist dans array
                    if(count($array_fr_id)==0){
                        array_push($array_fr_id,$fr->id);
                    }
                    else {
                        //si array.lenght!=0 test si id du frein exist dans array
                        for($i=0;$i<=sizeof($array_fr_id)-1;$i++){
                            if($array_fr_id[$i]==$fr->id){
                                $exist=1;
                            }
                        }
                        if($exist==0){
                            array_push($array_fr_id,$fr->id);
                        }
                    }

            }

        }
        //store to table frein_bien
        if(count($array_fr_id)>0){
            foreach($array_fr_id as $id_fr){
                //if bien_id already exist with this frein (en cas d update bien ->disponible)
                $count_exist_fr_bien=Frein_Bien::on('temp')->where('bien_id',$id)->where('frein_id',$id_fr)->count();
                if($count_exist_fr_bien==0){
                    FreinBienHelper::createFreinBien($bien->id,$id_fr);
                }
            }
        }
        return response()->json(['message' => $bien], 200);

    }

}
