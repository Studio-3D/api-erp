<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\DatabaseHelper;
use Illuminate\Console\Command;

class EcheanceEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:echeance-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $databases = DB::table('societes')->whereNull('deleted_at')->where('id', '>=', 233)->get();
        DatabaseHelper::envoyer_email_echeance($databases);
    }
}
