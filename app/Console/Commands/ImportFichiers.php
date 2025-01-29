<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Helpers\DatabaseHelper;
use App\Http\Helpers\ChroneJobHelpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;







class ImportFichiers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import_fichiers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commande pour importer les Fichiers';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        $databases = DB::table('societes')->whereNull('deleted_at')->get();
        DatabaseHelper::import_fichiers($databases);
    }
}
