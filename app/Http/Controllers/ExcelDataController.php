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
use App\Http\Helpers\ImportExcelHelper;
use App\Models\Projet;


class ExcelDataController extends Controller{

   
        
        
        
        

       
    public function UploadDataExcel(Request $request){
        // Retrieve project ID from the request
        
        $projet_id = $request->projetId;

        // Configure the database
        DatabaseHelper::Config();

        // Retrieve project from the database
        $projet = Projet::on('temp')->findOrFail($projet_id);

        // Set execution limits
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        // Retrieve data from the request
        $data = $request->input('data');

        // Extract keys from the first element of the data array
        $keys = array_keys($data[0]);
        
        Log::info($keys);
        

        // Determine import method based on key existence
        $importMethod = $this->determineImportMethod($keys);

        // Call the appropriate import method and return the result
        return ImportExcelHelper::$importMethod($data, $projet_id);
    }
    
        // Method to determine the import method based on key existence
        // private function determineImportMethod($keys) {
        //     if (in_array('tranche', $keys) && in_array('bloc', $keys) && in_array('immeuble', $keys)) {
                
        //         Log::info('ImportStockByProjet');
                
        //         return 'ImportStockByProjet';
        //     } elseif (in_array('tranche', $keys) && in_array('bloc', $keys)) {
        //         return 'ImportStockByProjetWithoutImmeuble';
        //     } elseif (in_array('tranche', $keys)) {
        //         Log::info('ImportStockByProjetWithoutBlocAndImmeuble');

        //         return 'ImportStockByProjetWithoutBlocAndImmeuble';
        //     } elseif (in_array('bloc', $keys) && in_array('immeuble', $keys)) {
        //         Log::info('ImportStockByProjetWithoutTranche');

        //         return 'ImportStockByProjetWithoutTranche';
        //     } elseif (in_array('bloc', $keys)) {
        //         Log::info('ImportStockByProjetWithoutTrancheAndImmeuble');

        //         return 'ImportStockByProjetWithoutTrancheAndImmeuble';
        //     } elseif (in_array('immeuble', $keys)) {
        //         Log::info('ImportStockByProjetWithoutTrancheAndBloc');


        //         return 'ImportStockByProjetWithoutTrancheAndBloc';
        //     } else {
        //         Log::info('ImportStockByProjetWithoutTrancheAndBlocAndImmeuble');

        //         return 'ImportStockByProjetWithoutTrancheAndBlocAndImmeuble';
        //     }
        // }
        private function determineImportMethod($keys) {
            $hasTranche = in_array('tranche', $keys);
            $hasBloc = in_array('Bloc', $keys);
            $hasImmeuble = in_array('immeuble', $keys);
        
            if ($hasTranche && $hasBloc && $hasImmeuble) {
                return 'ImportStockByProjet';
                Log::info('ImportStockByProjet');

            } elseif ($hasTranche && $hasBloc && !$hasImmeuble) {
                Log::info('ImportStockByProjetWithoutImmeuble');

                return 'ImportStockByProjetWithoutImmeuble';
            } elseif ($hasTranche && !$hasBloc && $hasImmeuble) {
                Log::info('ImportStockByProjetWithoutBloc');

                return 'ImportStockByProjetWithoutBloc';
            } elseif ($hasTranche && !$hasBloc && !$hasImmeuble) {
                Log::info('ImportStockByProjetWithoutBlocAndImmeuble');

                return 'ImportStockByProjetWithoutBlocAndImmeuble';
            } elseif (!$hasTranche && $hasBloc && $hasImmeuble) {
                Log::info('ImportStockByProjetWithoutTranche');

                return 'ImportStockByProjetWithoutTranche';
            } elseif (!$hasTranche && $hasBloc && !$hasImmeuble) {
                Log::info('ImportStockByProjetWithoutTrancheAndImmeuble');

                return 'ImportStockByProjetWithoutTrancheAndImmeuble';
            } elseif (!$hasTranche && !$hasBloc && $hasImmeuble) {
                Log::info('ImportStockByProjetWithoutTrancheAndBloc');

                return 'ImportStockByProjetWithoutTrancheAndBloc';
            } else {
                return 'ImportStockByProjetWithoutTrancheAndBlocAndImmeuble';
            }
        }
    }

           


    


