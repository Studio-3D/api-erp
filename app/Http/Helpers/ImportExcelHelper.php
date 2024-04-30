<?php

namespace App\Http\Helpers;

use App\Models\FreinEtage;
use App\Models\Frein;
use App\Models\Tranche;
use App\Models\Bien;
use App\Models\Bloc;
use App\Models\CompositionBien; 
use App\Models\TypeBien;
use App\Models\Immeuble;
use App\Http\Helpers\Bien_Helper;
use Illuminate\Support\Facades\Log;

class ImportExcelHelper
{
    public static function ImportStockByProjet($column, $projet_id)
    {
        Log::info($column['tranche']);   
        Log::info('tranche  colum n here ');
        $tranche =Tranche::on('temp')
        ->where('nom', $column['tranche'])
        ->where('projet_id', $projet_id)
        ->get();
        $count_tranche =Tranche::on('temp')
        ->where('nom', $column['tranche'])
        ->where('projet_id', $projet_id)
        ->count();
        log::info($column['tranche']);

        if($count_tranche>0)
        {
            Log::info('tranche from table db where  column  tranche here  exist');
            foreach($tranche as $tranches)
           {
                
                Log::info('tranche column here ');
                Log::info($tranches->id);
                
                $bloc = Bloc::on('temp')
                ->where('nom', $column['Bloc'])
                ->where('tranche_id', $tranches->id)
                ->where('projet_id', $projet_id)
                ->get();
                $count_bloc = Bloc::on('temp')
                ->where('nom', $column['Bloc'])
                ->where('tranche_id', $tranches->id)
                ->where('projet_id', $projet_id)
                ->count();
                if($count_bloc>0)
                {
                    Log::info('bloc exist from db  here where  column  tranche  here  exist');
       
                    foreach($bloc as $blocs)
                    {
                        
                        Log::info($blocs->id);
                        
                        $immeuble = Immeuble::on('temp')
                        ->where('nom', $column['immeuble'])
                        ->where('tranche_id',  $tranches->id)
                        ->where('projet_id', $projet_id)
                        ->where('bloc_id', $blocs->id)->get();

                        $count_immeuble = Immeuble::on('temp')
                        ->where('nom', $column['immeuble'])
                        ->where('tranche_id',  $tranches->id)
                        ->where('projet_id', $projet_id)
                        ->where('bloc_id', $blocs->id)->count();
                        if($count_immeuble>0)
                        {
                            Log::info('immeuble from db here  e where  column  tranche    exist ');
                           foreach($immeuble as $immeubles)
                           {
                            Log::info($immeubles->id);
                            Bien_Helper::checkAndCreateBien($tranches, $projet_id, $blocs, $immeubles, $column);
                           }
                        }
                        //  immeuble else

                        else{

                        log::info('im  in  else immeu  where column  tranche exist');
                           $immeuble=new Immeuble();
                           $immeuble->setConnection('temp');
                           $immeuble->nom=$column['immeuble'];
                           $immeuble->projet_id=$projet_id;
                           $immeuble->tranche_id=$tranches->id;
                           $immeuble->bloc_id=$blocs->id;
                           if($immeuble->save()){
                            $nv=0;
                            Bien_Helper::checkAndCreateBien($tranches, $projet_id, $blocs, $immeuble, $column);
                            }
                        }
                    }
                }
                // bloc else 
                else{
                    
                 Log::info('im  i   else bloc  where column  tranche exist');
                    
                  $bloc=new Bloc();
                  $bloc->setConnection('temp');
                  $bloc->nom=$column['Bloc'];
                  $bloc->projet_id=$projet_id;
                  $bloc->tranche_id=$tranches->id;
                  if($bloc->save()){
                    $immeuble=new Immeuble();
                    $immeuble->setConnection('temp');
                    $immeuble->nom=$column['immeuble'];
                    $immeuble->projet_id=$projet_id;
                    $immeuble->tranche_id=$tranches->id;
                    $immeuble->bloc_id=$bloc->id;
                    if($immeuble->save()){
                        // $bien_exist=Bien::on('temp')->where(function ($query ) use ($column){
                        //     $query->where('propriete_dite_bien',$column['Appt_Num'])->orwhere('propriete_dite_bien',$column['magasin_num']);
                        // })->where('tranche_id', $tranches->id)->where('projet_id', $projet_id)->where('bloc_id', $bloc->id)->where('immeuble_id',$immeuble->id)->count();

                        // in this case we  check if the  one of thatt columns  is  empty  not  have check itt   (med)
                        Bien_Helper::checkAndCreateBien($tranches, $projet_id, $bloc, $immeuble, $column);
                        
                    }
                    
                  }

                }
            }
        }
        // tranche else
        else{


            // tranche not exist   in database
            
            Log::info('tranche  not exist into  db start  create one   where column  tranche exist');
            
            log::info($column['tranche']);
            log::info($column['immeuble']);

      
          $new_tranche=new Tranche();
          $new_tranche->setConnection('temp');
          $new_tranche->nom=$column['tranche'];
          $new_tranche->projet_id=$projet_id;
          if($new_tranche->save()){
            log::info('store tranche succ  where column  tranche exist');
            $new_bloc=new Bloc();
            $new_bloc->setConnection('temp');
            $new_bloc->nom=$column['Bloc'];
            $new_bloc->projet_id=$projet_id;
            $new_bloc->tranche_id=$new_tranche->id;
            if($new_bloc->save() && $column['immeuble']!=null)
            log::info('after  bloc save succ  where column  tranche exist');

            {
                $new_immeuble=new Immeuble();
                $new_immeuble->setConnection('temp');
                $new_immeuble->nom=$column['immeuble'];
                $new_immeuble->projet_id=$projet_id;
                $new_immeuble->tranche_id=$new_tranche->id;
                $new_immeuble->bloc_id=$new_bloc->id;
            }
            if($new_immeuble->save()){
                log::info('after  imeeuble s save succ  where column  tranche exist');
                Bien_Helper::checkAndCreateBien($new_tranche, $projet_id, $new_bloc, $new_immeuble, $column);
                }
                  
            }
        }

    }
    public static function ImportStockByProjetWithoutTranche($column, $projet_id)
    {
          
        log::info('messing column tranche');

        $bloc = Bloc::on('temp')
        ->where('nom', $column['Bloc'])
        ->where('projet_id', $projet_id)
        ->get();
        $count_bloc = Bloc::on('temp')
        ->where('nom', $column['Bloc'])
        ->where('projet_id', $projet_id)
        ->count();
          
        if($count_bloc>0)
        {
            Log::info('bloc from db  where column  tranche  nottt exist ');

            foreach($bloc as $blocs)
            {
                
                Log::info($blocs->id);
                $immeuble = Immeuble::on('temp')
                ->where('nom', $column['immeuble'])
                ->where('projet_id', $projet_id)
                ->where('bloc_id', $blocs->id)->get();

                $immeuble_count = Immeuble::on('temp')
                ->where('nom', $column['immeuble'])
                ->where('projet_id', $projet_id)
                ->where('bloc_id', $blocs->id)->count();
            
                Log::info($immeuble);
                
                if($immeuble_count>0)
                {
                    Log::info('immeuble exist  from  db  where column  tranche  nottt exist');
                   foreach($immeuble as $immeubles)
                   {
                    
           
                    $nv=0;
                  
                    Bien_Helper::checkAndCreateBien2( $projet_id, $blocs, $immeubles, $column);
                   
                   }
                   
                }
                //  immeuble else

                else{

                    log::info('im  in  else imme   where column  tranche  nottt exist');
                   $immeuble=new Immeuble();
                   $immeuble->setConnection('temp');
                   $immeuble->nom=$column['immeuble'];
                   $immeuble->projet_id=$projet_id;
                   $immeuble->bloc_id=$blocs->id;
                   if($immeuble->save()){
                    $nv=0;
                    // $bien_exist=Bien::on('temp')->where(function ($query) use ($column){
                    //     $query->where('propriete_dite_bien',$column['Appt_Num'])->orwhere('propriete_dite_bien',$column['magasin_num']);
                    // })->where('tranche_id', $tranches->id)->where('projet_id', $projet_id)->where('bloc_id', $blocs->id)->where('immeuble_id', $immeubles->id)->count();
                    
                    // in this case we  check if the  one of thatt columns  is  empty  not  have check itt   (med)
                    
                    Bien_Helper::checkAndCreateBien2($projet_id, $blocs, $immeuble, $column);
                    }

                }

            }
        }
        // bloc else 
        else{
            
            Log::info('im  i   else bloc   where column  tranche  nottt exist');
            
         
          $bloc=new Bloc();
          $bloc->setConnection('temp');
          $bloc->nom=$column['Bloc'];
          $bloc->projet_id=$projet_id;
          if($bloc->save()){
            $immeuble=new Immeuble();
            $immeuble->setConnection('temp');
            $immeuble->nom=$column['immeuble'];
            $immeuble->projet_id=$projet_id;
            $immeuble->bloc_id=$bloc->id;
            if($immeuble->save()){
                // $bien_exist=Bien::on('temp')->where(function ($query ) use ($column){
                //     $query->where('propriete_dite_bien',$column['Appt_Num'])->orwhere('propriete_dite_bien',$column['magasin_num']);
                // })->where('tranche_id', $tranches->id)->where('projet_id', $projet_id)->where('bloc_id', $bloc->id)->where('immeuble_id',$immeuble->id)->count();

                // in this case we  check if the  one of thatt columns  is  empty  not  have check itt   (med)
                Bien_Helper::checkAndCreateBien2( $projet_id, $bloc, $immeuble, $column);

            }
            
          }

        
    }
    }
}
