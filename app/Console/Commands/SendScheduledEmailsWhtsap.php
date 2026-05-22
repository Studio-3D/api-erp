<?php

namespace App\Console\Commands;

use App\Http\Helpers\DatabaseHelper; // Modifiez pour correspondre à votre modèle utilisateur
use App\Mail\ScheduledEmail; // Mail à envoyer
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendScheduledEmailsWhtsap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    /**
     * Execute the console command.
     */

    protected $signature = 'app:send_scheduled_emails_whatsapp';
    protected $description = 'Envoyer des e-mails  WHSTAP rdv relance programmés à une date précise';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $databases = DB::table('societes')->whereNull('deleted_at')->get();
        DatabaseHelper::envoyer_whatsap_email_rdv_rlc($databases);
    }

}
