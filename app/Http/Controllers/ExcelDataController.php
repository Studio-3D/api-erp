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

        if(array_key_exists('tranche',$column) )
        {
            ImportExcelHelper::ImportStockByProjet($column, $projet_id);

        }
        else
            {
            
               
               
                    ImportExcelHelper::ImportStockByProjetWithoutTranche($column, $projet_id);

            
            }

        }
    
    }
   

   }


        
            // if tranche key  not exist
           


    


