<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User; // Modifiez pour correspondre à votre modèle utilisateur
use App\Mail\ScheduledEmail; // Mail à envoyer
use Illuminate\Support\Facades\Mail;
use App\Http\Helpers\DatabaseHelper;
use App\Http\Helpers\ChroneJobHelpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendScheduledEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    

    /**
     * Execute the console command.
     */
    
    protected $signature = 'emails:send-scheduled';
    protected $description = 'Envoyer des e-mails programmés à une date précise';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $databases = DB::table('societes')->whereNull('deleted_at')->get();
        DatabaseHelper::envoyer_email($databases);
    }
        
    
}
