<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Helpers\DatabaseHelper;
use App\Http\Helpers\RoleHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Http\Helpers\ImportExcelHelper;
class UploadBienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function upload(Request $request)
    {
        if (RoleHelper::ACSup()) {

            $projet_id = $request->projetId;

            DatabaseHelper::Config();

            $projet = Projet::on('temp')->findOrFail($projet_id);

            set_time_limit(0);
            ini_set('memory_limit', '-1');

            $data = $request->input('jsonData');

            $keys = array_keys($data[0]);

            //$importMethod = $this->determineImportMethod($keys);

           // ImportExcelHelper::$importMethod($data, $projet_id);


           $hasTranche = in_array('tranche', $keys);
           $hasBloc = in_array('bloc', $keys);
           $hasImmeuble = in_array('immeuble', $keys);
           //if excel containe column bloc or immeuble or tranche
           if ($hasTranche && $hasBloc && $hasImmeuble) {
               return ImportExcelHelper::ImportStockByProjet($data,$projet_id);

           } elseif ($hasTranche && $hasBloc && !$hasImmeuble) {
            return ImportExcelHelper::ImportStockByProjetWithoutImmeuble($data,$projet_id);

           } elseif ($hasTranche && !$hasBloc && $hasImmeuble) {
               return ImportExcelHelper::ImportStockByProjetWithoutBloc($data,$projet_id);
           } elseif ($hasTranche && !$hasBloc && !$hasImmeuble) {
            return ImportExcelHelper::ImportStockByProjetWithoutBlocAndImmeuble($data,$projet_id);

           } elseif (!$hasTranche && $hasBloc && $hasImmeuble) {
            return ImportExcelHelper::ImportStockByProjetWithoutTranche($data,$projet_id);

           } elseif (!$hasTranche && $hasBloc && !$hasImmeuble) {
            return ImportExcelHelper::ImportStockByProjetWithoutTrancheAndImmeuble($data,$projet_id);

           } elseif (!$hasTranche && !$hasBloc && $hasImmeuble) {
               return ImportExcelHelper::ImportStockByProjetWithoutTrancheAndBloc($data,$projet_id);
           } else {
               return ImportExcelHelper::ImportStockByProjetWithoutTrancheAndBlocAndImmeuble($data,$projet_id);
           }
            return response()->json('done');
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

    }




        private function determineImportMethod($keys) {
            $hasTranche = in_array('tranche', $keys);
            $hasBloc = in_array('Bloc', $keys);
            $hasImmeuble = in_array('immeuble', $keys);

            if ($hasTranche && $hasBloc && $hasImmeuble) {
                return 'ImportStockByProjet';


            } elseif ($hasTranche && $hasBloc && !$hasImmeuble) {


                return 'ImportStockByProjetWithoutImmeuble';
            } elseif ($hasTranche && !$hasBloc && $hasImmeuble) {


                return 'ImportStockByProjetWithoutBloc';
            } elseif ($hasTranche && !$hasBloc && !$hasImmeuble) {

                return 'ImportStockByProjetWithoutBlocAndImmeuble';
            } elseif (!$hasTranche && $hasBloc && $hasImmeuble) {

                return 'ImportStockByProjetWithoutTranche';
            } elseif (!$hasTranche && $hasBloc && !$hasImmeuble) {

                return 'ImportStockByProjetWithoutTrancheAndImmeuble';
            } elseif (!$hasTranche && !$hasBloc && $hasImmeuble) {

                return 'ImportStockByProjetWithoutTrancheAndBloc';
            } else {
                return 'ImportStockByProjetWithoutTrancheAndBlocAndImmeuble';
            }
        }


    /**
     * Store a newly created resource in storage.
     */


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (RoleHelper::AdminSup()) {
            DatabaseHelper::Config();

            $user = Auth::user();
            DatabaseHelper::Config();
            $userAuth = User::on('temp')->where('user_id_origin', $user->getAuthIdentifier())->get();
            $user_societes = User::where('id', $userAuth->value('user_id_origin'))->first();
            $societe = Societe::findOrfail($user_societes->societe_id);
            $cps = Cps::on('temp')->findOrFail($id);
            $cps->nature_travaux = $request->nature_travaux;
            $cps->cout = $request->cout;
            $cps->date_validation = $request->date_validation;
            $cps->projet_id = $request->projet_id;
            $cps->user_id=$userAuth->value('id');
            if ($request->hasFile('piece_jointe')) {
                $cps->piece_jointe = $request->file('piece_jointe')->getClientOriginalName();;
                $directory = public_path('Docs/' . $societe->raison_sociale_concatene . '_' . $societe->id . '/cps');
                File::makeDirectory($directory, 0755, true, true);
                $request->file('piece_jointe')->move($directory,$request->file('piece_jointe')->getClientOriginalName());
            }
            if ($cps->save()) {
                return response()->json(['cps' => $cps], 200);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Remove the specified resource from storage.
     */

}

