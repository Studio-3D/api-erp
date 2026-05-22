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
    protected $signature = 'app:send_email_whatsapp_echeance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send whatsap email for echeances Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $databases = DB::table('societes')
        ->whereNull('deleted_at')
        //->where('id', '=', 233)
        ->get();
        DatabaseHelper::envoyer_email_whatsapp_echeance($databases);
    }
}
