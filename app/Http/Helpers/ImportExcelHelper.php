<?php

namespace App\Http\Helpers;

use App\Models\FreinEtage;
use App\Models\Frein;
use App\Models\Tranche;
use App\Models\Bien;
use App\Models\Bloc;
use App\Models\Projet;
use App\Http\Helpers\DatabaseHelper;
use App\Models\CompositionBien;
use App\Models\TypeBien;
use App\Models\Immeuble;
use App\Http\Helpers\Bien_Helper;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Societe;
use App\Models\Import;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class ImportExcelHelper {

    public static function store_fichier_import(Request $req){

        $user = Auth::user();
        DatabaseHelper::Config();
        $userAuth = User::on('temp')->where('user_id_origin', $user->getAuthIdentifier())->get();
        $user_societes = User::where('id', $userAuth->value('user_id_origin'))->first();
        $societe = Societe::findOrfail($user_societes->societe_id);
        $imp = new Import();
        $imp->setConnection('temp');
        $imp->projet_id = $req->projet_id;
        $imp->statut='0';
        $imp->user_id=$userAuth->value('id');
        $imp->data=$req->data;
        if ($req->file->hasFile('piece_jointe')) {
            $client_origin_name=$req->file->file('piece_jointe')->getClientOriginalName();
            $date=str_replace(str_split('\\/:*?"<>|+-\s+'), '_', date("Y-m-d H:i:s"));
            $filename = pathinfo($client_origin_name, PATHINFO_FILENAME).'_'.$date;
            $extension = pathinfo($client_origin_name, PATHINFO_EXTENSION);
            $imp->fichier =$filename.'.'.$extension;
            $directory = public_path('Docs/' . $societe->raison_sociale_concatene . '_' . $societe->id . '/Import_fichier');
            File::makeDirectory($directory, 0755, true, true);
            $req->file->file('piece_jointe')->move($directory,$filename.'.'.$extension);
        }
        $imp->save();
    }
    public static function ImportStockByProjetWithoutTrancheAndBlocAndImmeuble($request,$data,$projet_id,$console){

        //get function pour stocker le fichier en serveur et en Base de donne
        if($console==0){
            DatabaseHelper::Config();

            $projet=Projet::on('temp')->findOrfail($projet_id);
            $req=new \Illuminate\Http\Request();
            if($projet->nbre_tranches==0 && $projet->nbre_blocs==0 && $projet->nbre_immeubles==0)
            {
                //store le fichier en serveur et base de donne
                $data = [
                    'file' => $request,
                    'projet_id' => $projet_id,
                    'data'=>$data,
                ];

                $foobar = new ImportExcelHelper();  // correct
                $foobar->store_fichier_import($req->merge($data));
                return response()->json('le fichier est en cours d\'importation');


            }
            else{
                return response()->json(['error' => 'Aucune Colonne Tranche Bloc Immeuble n\'est requise pour ce Projet.'], 400);
            }
        }else{
            //stock donne en Bse de donne by cronjob

               foreach($data as $row)
                {
                    Bien_Helper::checkAndCreateBienByExcel($projet_id, null,  null, null, $row);
                }
                return response()->json('done');
        }
    }


    public static function ImportStockByProjetWithoutTrancheAndBloc($request,$data,$projet_id,$console){

        //get function pour stocker le fichier en serveur et en Base de donne
        if($console==0){
            DatabaseHelper::Config();
            $req=new \Illuminate\Http\Request();
            $projet = Projet::on('temp')->findOrfail($projet_id);
            if($projet->nbre_blocs==0 && $projet->nbre_tranches==0 && $projet->nbre_immeubles>0){
                //store le fichier en serveur et base de donne
                $data = [
                    'file' => $request,
                    'projet_id' => $projet_id,
                    'data'=>$data,
                ];

                $foobar = new ImportExcelHelper();  // correct
                $foobar->store_fichier_import($req->merge($data));
                return response()->json('le fichier est en cours d\'importation');

            }else{
                return response()->json(['error' => 'Les Colonnes Tranche Bloc ne sont pas  requises pour Ce Projet.'], 400);

            }
        }else{
            //stock donne en Bse de donne by cronjob
             foreach($data  as $row){
                    $immeuble = Immeuble::on('temp')
                                        ->where('nom', $row['Immeuble'])
                                        ->where('projet_id', $projet_id)
                                        ->first();
                    if(!$immeuble) {
                        $immeuble=new Immeuble();
                        $immeuble->setConnection('temp');
                        $immeuble->nom=$row['Immeuble'];
                        $immeuble->projet_id=$projet_id;
                        $immeuble->save();
                    }
                    Bien_Helper::checkAndCreateBienByExcel($projet_id, null,  null, $immeuble->id, $row);
                }

        }
    }

    public static function ImportStockByProjetWithoutTrancheAndImmeuble($request,$data,$projet_id,$console){


        //get function pour stocker le fichier en serveur et en Base de donne
        if($console==0){

            DatabaseHelper::Config();
            $projet = Projet::on('temp')->findOrfail($projet_id);
            $req=new \Illuminate\Http\Request();
            if($projet->nbre_tranches==0 && $projet->nbre_immeubles==0 && $projet->nbre_blocs>0){
                //store le fichier en serveur et base de donne
                $data = [
                    'file' => $request,
                    'projet_id' => $projet_id,
                    'data'=>$data,
                ];

                $foobar = new ImportExcelHelper();  // correct
                $foobar->store_fichier_import($req->merge($data));
                return response()->json('le fichier est en cours d\'importation');

            }else{
                return response()->json(['error' => 'Les Colonnes Tranche Immeuble ne sont pas requises pour Ce Projet.'], 400);

            }
        }
        else{
            //stock donne en Bse de donne by cronjob
            foreach($data as $row){
                $bloc = Bloc::on('temp')
                                ->where('nom', $row['Bloc'])
                                ->where('projet_id', $projet_id)
                                ->first();
                if(!$bloc){
                    $bloc=new Bloc();
                    $bloc->setConnection('temp');
                    $bloc->nom=$row['Bloc'];
                    $bloc->projet_id=$projet_id;
                    $bloc->save();
                }
                Bien_Helper::checkAndCreateBienByExcel($projet_id, null, $bloc->id, null, $row);
            }
        }
    }

    public static function ImportStockByProjetWithoutTranche($request,$data,$projet_id,$console){

        //get function pour stocker le fichier en serveur et en Base de donne
        if($console==0){
            DatabaseHelper::Config();

            $projet = Projet::on('temp')->findOrfail($projet_id);
            $req=new \Illuminate\Http\Request();
            log::info($projet);
            if($projet->nbre_tranches==0 && $projet->nbre_blocs>0 && $projet->nbre_immeubles>0){
                //store le fichier en serveur et base de donne
                $data = [
                    'file' => $request,
                    'projet_id' => $projet_id,
                    'data'=>$data,
                ];

                $foobar = new ImportExcelHelper();  // correct
                $foobar->store_fichier_import($req->merge($data));

                return response()->json('le fichier est en cours d\'importation');
            }else{
                return response()->json(['error' => 'La Colonne Tranche n\'est pas requise pour le fichier.'], 400);

            }
        }else{
            //stock donne en Bse de donne by cronjob

             foreach($data as $row){
                    $bloc = Bloc::on('temp')
                                    ->where('nom', $row['Bloc'])
                                    ->where('projet_id', $projet_id)
                                    ->first();
                    if(!$bloc){
                        $bloc=new Bloc();
                        $bloc->setConnection('temp');
                        $bloc->nom=$row['Bloc'];
                        $bloc->projet_id=$projet_id;
                        $bloc->save();
                    }
                    $immeuble = Immeuble::on('temp')
                                        ->where('nom', $row['Immeuble'])
                                        ->where('projet_id', $projet_id)
                                        ->where('bloc_id', $bloc->id)
                                        ->first();
                    if(!$immeuble){
                        $immeuble=new Immeuble();
                        $immeuble->setConnection('temp');
                        $immeuble->nom=$row['Immeuble'];
                        $immeuble->projet_id=$projet_id;
                        $immeuble->bloc_id=$bloc->id;
                        $immeuble->save();
                    }
                    Bien_Helper::checkAndCreateBienByExcel($projet_id, null, $bloc->id, $immeuble->id, $row);
                }
        }
    }

    public static function ImportStockByProjetWithoutBlocAndImmeuble($request,$data,$projet_id,$console){

        //get function pour stocker le fichier en serveur et en Base de donne
        if($console==0){

            DatabaseHelper::Config();

            $projet = Projet::on('temp')->findOrfail($projet_id);
            $req=new \Illuminate\Http\Request();
            if($projet->nbre_blocs==0 && $projet->nbre_immeubles==0 && $projet->nbre_tranches>0){
                //store le fichier en serveur et base de donne
                $data = [
                    'file' => $request,
                    'projet_id' => $projet_id,
                    'data'=>$data,
                ];

                $foobar = new ImportExcelHelper();  // correct
                $foobar->store_fichier_import($req->merge($data));

                return response()->json('le fichier est en cours d\'importation');
            }else{
                return response()->json(['error' => 'Les Colonnes Immeuble Bloc ne sont pas requise pour le fichier.'], 400);

            }
        }else{
            //stock donne en Bse de donne by cronjob
             foreach($data as $row){
                    $tranche =Tranche::on('temp')
                                    ->where('nom', $row['Tranche'])
                                    ->where('projet_id', $projet_id)
                                    ->first();
                    if(!$tranche){
                        $tranche=new Tranche();
                        $tranche->setConnection('temp');
                        $tranche->nom=$row['Tranche'];
                        $tranche->projet_id=$projet_id;
                        $tranche->date_lancement=Carbon::now();
                        $tranche->date_livraison=Carbon::now();
                        $tranche->niveau_etages=0;
                        $tranche->save();
                    }
                    Bien_Helper::checkAndCreateBienByExcel($projet_id, $tranche->id, null, null, $row);
                }
        }
    }

    public static function ImportStockByProjetWithoutBloc($request,$data,$projet_id,$console){

          //get function pour stocker le fichier en serveur et en Base de donne
          if($console==0){

            DatabaseHelper::Config();
            $req=new \Illuminate\Http\Request();
            $projet = Projet::on('temp')->findOrfail($projet_id);
            if($projet->nbre_blocs==0 && $projet->nbre_tranches>0 && $projet->nbre_immeubles>0){
                //store le fichier en serveur et base de donne
                $data = [
                    'file' => $request,
                    'projet_id' => $projet_id,
                    'data'=>$data,
                ];

                $foobar = new ImportExcelHelper();  // correct
                $foobar->store_fichier_import($req->merge($data));

                return response()->json('le fichier est en cours d\'importation');

            }else{
                return response()->json(['error' => 'La Colonne Bloc n\'est pas requise pour le fichier.'], 400);

            }
        }else {
            //stock donne en Bse de donne by cronjob
            foreach($data as $row){
                    $tranche =Tranche::on('temp')
                                    ->where('nom', $row['Tranche'])
                                    ->where('projet_id', $projet_id)
                                    ->first();
                    if(!$tranche){
                        $tranche=new Tranche();
                        $tranche->setConnection('temp');
                        $tranche->nom=$row['Tranche'];
                        $tranche->projet_id=$projet_id;
                        $tranche->date_lancement=Carbon::now();
                        $tranche->date_livraison=Carbon::now();
                        $tranche->niveau_etages=0;
                        $tranche->save();
                    }
                    $immeuble = Immeuble::on('temp')
                                        ->where('nom', $row['Immeuble'])
                                        ->where('tranche_id',  $tranche->id)
                                        ->where('projet_id', $projet_id)
                                        ->first();
                    if(!$immeuble){
                            $immeuble=new Immeuble();
                            $immeuble->setConnection('temp');
                            $immeuble->nom=$row['Immeuble'];
                            $immeuble->projet_id=$projet_id;
                            $immeuble->tranche_id=$tranche->id;
                            $immeuble->save();

                    }
                    Bien_Helper::checkAndCreateBienByExcel( $projet_id, $tranche->id, null, $immeuble->id, $row);

                }
        }
    }

    public static function ImportStockByProjetWithoutImmeuble($request,$data,$projet_id,$console){
         //get function pour stocker le fichier en serveur et en Base de donne
        if($console==0){
            DatabaseHelper::Config();
            $req=new \Illuminate\Http\Request();
            $projet = Projet::on('temp')->findOrfail($projet_id);
            if($projet->nbre_immeubles==0 && $projet->nbre_tranches>0 && $projet->nbre_blocs>0){
                //store le fichier en serveur et base de donne
                $data = [
                    'file' => $request,
                    'projet_id' => $projet_id,
                    'data'=>$data,
                ];

                $foobar = new ImportExcelHelper();  // correct
                $foobar->store_fichier_import($req->merge($data));
                return response()->json('le fichier est en cours d\'importation');

            }else{
                return response()->json(['error' => 'La Colonne Immeuble n\'est pas requise pour le fichier.'], 400);
            }
        }else{
            //stock donne en Bse de donne by cronjob
              foreach($data as $row){
                    $tranche =Tranche::on('temp')
                                    ->where('nom', $row['Tranche'])
                                    ->where('projet_id', $projet_id)
                                    ->first();
                    if(!$tranche){
                        $tranche=new Tranche();
                        $tranche->setConnection('temp');
                        $tranche->nom=$row['Tranche'];
                        $tranche->projet_id=$projet_id;
                        $tranche->date_lancement=Carbon::now();
                        $tranche->date_livraison=Carbon::now();
                        $tranche->niveau_etages=0;
                        $tranche->save();
                    }
                    $bloc = Bloc::on('temp')
                                    ->where('nom', $row['Bloc'])
                                    ->where('tranche_id', $tranche->id)
                                    ->where('projet_id', $projet_id)
                                    ->first();
                    if(!$bloc){
                        $bloc=new Bloc();
                        $bloc->setConnection('temp');
                        $bloc->nom=$row['Bloc'];
                        $bloc->projet_id=$projet_id;
                        $bloc->tranche_id=$tranche->id;
                        $bloc->save();
                    }

                    Bien_Helper::checkAndCreateBienByExcel($projet_id, $tranche->id, $bloc->id, null, $row);
                }
        }


    }

    public static function ImportStockByProjet($request,$data,$projet_id,$console){
         //get function pour stocker le fichier en serveur et en Base de donne
        if($console==0){

            DatabaseHelper::Config();
            $req=new \Illuminate\Http\Request();
            $projet = Projet::on('temp')->findOrfail($projet_id);
            if($projet->nbre_tranches>0 && $projet->nbre_blocs>0 && $projet->nbre_immeubles>0){
                //store le fichier en serveur et base de donne
                $data = [
                    'file' => $request,
                    'projet_id' => $projet_id,
                    'data'=>$data,
                ];

                $foobar = new ImportExcelHelper();  // correct
                $foobar->store_fichier_import($req->merge($data));



             return response()->json('le fichier est en cours d\'importation');
                /*foreach($data as $row){
                    $tranche =Tranche::on('temp')
                                    ->where('nom', $row['Tranche'])
                                    ->where('projet_id', $projet_id)
                                    ->first();
                    if(!$tranche){
                        $tranche=new Tranche();
                        $tranche->setConnection('temp');
                        $tranche->nom=$row['Tranche'];
                        $tranche->projet_id=$projet_id;
                        $tranche->date_lancement=Carbon::now();
                        $tranche->date_livraison=Carbon::now();
                        $tranche->niveau_etages=0;
                        $tranche->save();
                    }
                    $bloc = Bloc::on('temp')
                                    ->where('nom', $row['Bloc'])
                                    ->where('tranche_id', $tranche->id)
                                    ->where('projet_id', $projet_id)
                                    ->first();
                    if(!$bloc){
                        $bloc=new Bloc();
                        $bloc->setConnection('temp');
                        $bloc->nom=$row['Bloc'];
                        $bloc->projet_id=$projet_id;
                        $bloc->tranche_id=$tranche->id;
                        $bloc->save();
                    }
                    $immeuble = Immeuble::on('temp')
                                        ->where('nom', $row['Immeuble'])
                                        ->where('tranche_id',  $tranche->id)
                                        ->where('bloc_id', $bloc->id)
                                        ->where('projet_id', $projet_id)
                                        ->first();
                    if(!$immeuble){
                        $immeuble=new Immeuble();
                        $immeuble->setConnection('temp');
                        $immeuble->nom=$row['Immeuble'];
                        $immeuble->projet_id=$projet_id;
                        $immeuble->tranche_id=$tranche->id;
                        $immeuble->bloc_id=$bloc->id;
                        $immeuble->save();
                    }
                    Bien_Helper::checkAndCreateBienByExcel($projet_id, $tranche->id, $bloc->id, $immeuble->id, $row);
                }*/
            }else{
                return response()->json(['error' => 'Le fichier nécessite les Colonnes tranche Bloc Immeuble.'], 400);

            }

        }else{
        //stock donne en Bse de donne by cronjob

            foreach($data as $row){
                $tranche =Tranche::on('temp')
                                ->where('nom', $row['Tranche'])
                                ->where('projet_id', $projet_id)
                                ->first();
                if(!$tranche){
                    $tranche=new Tranche();
                    $tranche->setConnection('temp');
                    $tranche->nom=$row['Tranche'];
                    $tranche->projet_id=$projet_id;
                    $tranche->date_lancement=Carbon::now();
                    $tranche->date_livraison=Carbon::now();
                    $tranche->niveau_etages=0;
                    $tranche->save();
                }
                $bloc = Bloc::on('temp')
                                ->where('nom', $row['Bloc'])
                                ->where('tranche_id', $tranche->id)
                                ->where('projet_id', $projet_id)
                                ->first();
                if(!$bloc){
                    $bloc=new Bloc();
                    $bloc->setConnection('temp');
                    $bloc->nom=$row['Bloc'];
                    $bloc->projet_id=$projet_id;
                    $bloc->tranche_id=$tranche->id;
                    $bloc->save();
                }
                $immeuble = Immeuble::on('temp')
                                    ->where('nom', $row['Immeuble'])
                                    ->where('tranche_id',  $tranche->id)
                                    ->where('bloc_id', $bloc->id)
                                    ->where('projet_id', $projet_id)
                                    ->first();
                if(!$immeuble){
                    $immeuble=new Immeuble();
                    $immeuble->setConnection('temp');
                    $immeuble->nom=$row['Immeuble'];
                    $immeuble->projet_id=$projet_id;
                    $immeuble->tranche_id=$tranche->id;
                    $immeuble->bloc_id=$bloc->id;
                    $immeuble->save();
                }
                Bien_Helper::checkAndCreateBienByExcel($projet_id, $tranche->id, $bloc->id, $immeuble->id, $row);
            }

        }

    }

}
