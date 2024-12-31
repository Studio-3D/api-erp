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




       /* private function determineImportMethod($keys) {
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
        }*/


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
  

    /**
     * Remove the specified resource from storage.
     */

}

