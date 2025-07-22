<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\LinkedIn\LinkedInController;

class PollLinkedInStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'linkedin:poll-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Poll LinkedIn API for post statistics and analytics every 5 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting LinkedIn stats polling...');
        
        try {
            $controller = new LinkedInController();
            $response = $controller->pollLinkedInStats();
            
            if ($response->getStatusCode() === 200) {
                $this->info('LinkedIn stats polling completed successfully');
            } else {
                $this->error('LinkedIn stats polling failed');
            }
        } catch (\Exception $e) {
            $this->error('LinkedIn stats polling error: ' . $e->getMessage());
        }
    }
}
