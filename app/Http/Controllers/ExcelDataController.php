<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Tranche;
use App\Models\Bien;
use App\Models\Bloc;
use App\Models\CompositionBien; 
use App\Models\TypeBien;
use App\Models\Immeuble;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingcolumn;
use Illuminate\Support\Str;
use App\Http\Helpers\Bien_Helper;
use App\Http\Helpers\DatabaseHelper;




class ExcelDataController extends Controller
{

   
    public function UploadDataExcel(Request $request)
    {
        $projet_id = $request->projetId;
        DatabaseHelper::Config();
        set_time_limit(0);
        ini_set('memory_limit', '-1');
    
         $data = $request->input('data');
       
        foreach ($data as $column) {

            if(array_key_exists('tranche',$column))
            {
                
            $tranche =Tranche::on('temp')
            ->where('nom', $column['tranche'])
            ->where('projet_id', $projet_id)
            ->get();
            log::info($tranche);
            //   we have  check  if tranche exist  into file excel 
            if($tranche)
            {
                Log::info('tranche exist ');

                foreach($tranche as $tranches)
                {
                    
                    Log::info(' loop tranche');
                
                    Log::info($tranches->id);
                    
                    $bloc = Bloc::on('temp')
                    ->where('nom', $column['Bloc'])
                    ->where('tranche_id', $tranches->id)
                    ->where('projet_id', $projet_id)
                    ->get();

               Log::info($bloc);
               

                    if($bloc)
                    {
                        Log::info('bloc exist ');
           
                        foreach($bloc as $blocs)
                        {
                            
                            Log::info('loop bloc ');
                            
                            
                            Log::info($blocs->id);
                            
                            $immeuble = Immeuble::on('temp')
                            ->where('nom', $column['immeuble'])
                            ->where('tranche_id',  $tranches->id)
                            ->where('projet_id', $projet_id)
                            ->where('bloc_id', $blocs->id)->get();
                        
                            Log::info($immeuble);
                            
                            if($immeuble)
                            {
                                Log::info('immeuble exist ');
                               foreach($immeuble as $immeubles)
                               {
                                
                                Log::info(' lop immeu');
                                Log::info($immeubles->id);
                                $nv=0;
                                
                                Bien_Helper::checkAndCreateBien($tranches, $projet_id, $blocs, $immeubles, $column);
                                log::info(' bien  exist ');
                               }
                               
                            }
                            //  immeuble else

                            else{

                                log::info('im  in  else imme');
                               $immeuble=new Immeuble();
                               $immeuble->setConnection('temp');
                               $immeuble->nom=$column['immeuble'];
                               $immeuble->projet_id=$projet_id;
                               $immeuble->tranche_id=$tranches->id;
                               $immeuble->bloc_id=$blocs->id;
                               if($immeuble->save()){
                                $nv=0;
                                // $bien_exist=Bien::on('temp')->where(function ($query) use ($column){
                                //     $query->where('propriete_dite_bien',$column['Appt_Num'])->orwhere('propriete_dite_bien',$column['magasin_num']);
                                // })->where('tranche_id', $tranches->id)->where('projet_id', $projet_id)->where('bloc_id', $blocs->id)->where('immeuble_id', $immeubles->id)->count();
                                
                                // in this case we  check if the  one of thatt columns  is  empty  not  have check itt   (med)
                                
                                Bien_Helper::checkAndCreateBien($tranches, $projet_id, $blocs, $immeubles, $column);
                                }

                            }

                        }
                    }
                    // bloc else 
                    else{
                        
                        Log::info('im  i   else bloc');
                        
                        $nv=null;
                        // bloc not exist 
                      $bloc=new Bloc();
                      $bloc->setConnetion('temp');
                      $bloc->nom=$column['bloc'];
                      $bloc->projet_id=$projet_id;
                      $bloc->tranche_id=$tranches->id;
                      if($bloc->save()){
                        $immeuble=new Immeuble();
                        $immeuble->setConnetion('temp');
                        $immeuble->nom=$column['immeuble'];
                        $immeuble->projet_id=$projet_id;
                        $immeuble->tranche_id=$tranches->id;
                        $immeuble->bloc_id=$bloc->id;
                        if($immeuble->save()){
                            // $bien_exist=Bien::on('temp')->where(function ($query ) use ($column){
                            //     $query->where('propriete_dite_bien',$column['Appt_Num'])->orwhere('propriete_dite_bien',$column['magasin_num']);
                            // })->where('tranche_id', $tranches->id)->where('projet_id', $projet_id)->where('bloc_id', $bloc->id)->where('immeuble_id',$immeuble->id)->count();

                            // in this case we  check if the  one of thatt columns  is  empty  not  have check itt   (med)
                            Bien_Helper::checkAndCreateBien($tranches, $projet_id, $blocs, $immeubles, $column);

                        }
                        
                      }

                    }
                }
            }
            // tranche else
            else{

                // tranche not exist   in database
                
              $nv=null;
              $tranche=new Tranche();
              $tranche->setConnection('temp');
              $tranche->nom=$column['tranche'];
              $tranche->projet_id=$projet_id;
              if($tranche->save){

                $bloc=new Bloc();
                $bloc->setConnection('temp');
                $bloc->nom=$column['bloc'];
                $bloc->projet_id=$projet_id;
                $bloc->tranche_id=$tranche->id;
                if($bloc->save())
                {
                    $immeuble=new Immeuble();
                    $immeuble->setConnection('temp');
                    $immeuble->nom=$row['immeuble'];
                    $immeuble->projet_id=$projet_id;
                    $immeuble->tranche_id=$tranche->id;
                    $immeuble->bloc_id=$bloc->id;
                }
                if($immeuble->save()){
                    // $bien_exist=Bien::on('temp')->where(function ($query ) use ($column){
                    //     $query->where('propriete_dite_bien',$column['Appt_Num'])->orwhere('propriete_dite_bien',$column['magasin_num']);
                    // })->where('tranche_id', $tranche->id)->where('projet_id', $projet_id)->where('bloc_id', $bloc->id)->where('immeuble_id',$immeuble->id)->count();

                    // in this case we  check if the  one of thatt columns  is  empty  not  have check itt   (med)
                                
                                Bien_Helper::checkAndCreateBien($tranches, $projet_id, $blocs, $immeubles, $column);
                    }
                      
                        }}

            }
            // if tranche key  not exist
            else{
                log::info(' messing column tranche');
                $bloc = Bloc::on('temp')
                ->where('nom', $column['Bloc'])
                ->where('projet_id', $projet_id)
                ->get();

           Log::info($bloc);
           

                if($bloc)
                {
                    Log::info('bloc exist ');
       
                    foreach($bloc as $blocs)
                    {
                        
                        Log::info('loop bloc ');
                        
                        
                        Log::info($blocs->id);
                        
                        $immeuble = Immeuble::on('temp')
                        ->where('nom', $column['immeuble'])
                        ->where('projet_id', $projet_id)
                        ->where('bloc_id', $blocs->id)->get();
                    
                        Log::info($immeuble);
                        
                        if($immeuble)
                        {
                            Log::info('immeuble exist ');
                           foreach($immeuble as $immeubles)
                           {
                            
                            Log::info(' lop immeu');
                            Log::info($immeubles->id);
                            $nv=0;
                            $tranches=Null;
                            Bien_Helper::checkAndCreateBien($tranches, $projet_id, $blocs, $immeubles, $column);
                            log::info(' bien  exist ');
                           }
                           
                        }
                        //  immeuble else

                        else{

                            log::info('im  in  else imme');
                           $immeuble=new Immeuble();
                           $immeuble->setConnection('temp');
                           $immeuble->nom=$column['immeuble'];
                           $immeuble->projet_id=$projet_id;
                           $immeuble->tranche_id=$tranches;
                           $immeuble->bloc_id=$blocs->id;
                           if($immeuble->save()){
                            $nv=0;
                            // $bien_exist=Bien::on('temp')->where(function ($query) use ($column){
                            //     $query->where('propriete_dite_bien',$column['Appt_Num'])->orwhere('propriete_dite_bien',$column['magasin_num']);
                            // })->where('tranche_id', $tranches->id)->where('projet_id', $projet_id)->where('bloc_id', $blocs->id)->where('immeuble_id', $immeubles->id)->count();
                            
                            // in this case we  check if the  one of thatt columns  is  empty  not  have check itt   (med)
                            
                            Bien_Helper::checkAndCreateBien($tranches, $projet_id, $blocs, $immeubles, $column);
                            }

                        }

                    }
                }
                // bloc else 
                else{
                    
                    Log::info('im  i   else bloc');
                    
                    $nv=null;
                    // bloc not exist 
                  $bloc=new Bloc();
                  $bloc->setConnetion('temp');
                  $bloc->nom=$column['bloc'];
                  $bloc->projet_id=$projet_id;
                  $bloc->tranche_id=$tranches->id;
                  if($bloc->save()){
                    $immeuble=new Immeuble();
                    $immeuble->setConnetion('temp');
                    $immeuble->nom=$column['immeuble'];
                    $immeuble->projet_id=$projet_id;
                    $immeuble->tranche_id=$tranches->id;
                    $immeuble->bloc_id=$bloc->id;
                    if($immeuble->save()){
                        // $bien_exist=Bien::on('temp')->where(function ($query ) use ($column){
                        //     $query->where('propriete_dite_bien',$column['Appt_Num'])->orwhere('propriete_dite_bien',$column['magasin_num']);
                        // })->where('tranche_id', $tranches->id)->where('projet_id', $projet_id)->where('bloc_id', $bloc->id)->where('immeuble_id',$immeuble->id)->count();

                        // in this case we  check if the  one of thatt columns  is  empty  not  have check itt   (med)
                        Bien_Helper::checkAndCreateBien($tranches, $projet_id, $blocs, $immeubles, $column);

                    }
                    
                  }

                
            }

            }

           
    
        }
        Log::info('blocs: ',$blocs);
    }

    

}

