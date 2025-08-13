<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use App\Models\V1\Societe;
use App\Http\Helpers\DatabaseHelper;

class EnsureSeenColumnInNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:ensure-seen-column';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure the seen column exists in notifications table for all societe tenant databases';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to ensure seen column exists in notifications table for all societe databases...');

        try {
            // Get all societes
            $societes = Societe::all();
            $this->info("Found {$societes->count()} societe(s) to process.");

            foreach ($societes as $societe) {
                $databaseName = 'Erp_' . $societe->raison_sociale_concatene . '_' . $societe->id;
                $this->info("Processing societe: {$societe->raison_sociale} (DB: {$databaseName})");

                try {
                    // Set up the database connection using DatabaseHelper pattern
                    $connection = DatabaseHelper::Connection_database($databaseName);
                    config(['database.connections.temp' => $connection]);
                    DB::purge('temp');
                    DB::reconnect('temp');

                    // Verify connection
                    $actualDbName = DB::connection('temp')->getDatabaseName();
                    $this->info("  Connected to database: {$actualDbName}");

                    // Check if notifications table exists
                    if (Schema::connection('temp')->hasTable('notifications')) {
                        // Check if seen column exists
                        if (!Schema::connection('temp')->hasColumn('notifications', 'seen')) {
                            $this->info("  Adding seen column to notifications table...");

                            Schema::connection('temp')->table('notifications', function ($table) {
                                $table->boolean('seen')->default(false)->after('traite_appel_id');
                            });

                            $this->info("  ✓ Seen column added successfully");
                        } else {
                            $this->info("  ✓ Seen column already exists");
                        }

                        // Also drop seen_notifications table if it exists
                        if (Schema::connection('temp')->hasTable('seen_notifications')) {
                            $this->info("  Dropping seen_notifications table...");
                            Schema::connection('temp')->dropIfExists('seen_notifications');
                            $this->info("  ✓ seen_notifications table dropped");
                        }

                    } else {
                        $this->warn("  ⚠ Notifications table does not exist in this database");
                    }

                } catch (\Exception $e) {
                    $this->error("  ✗ Error processing {$societe->raison_sociale}: " . $e->getMessage());
                }
            }

            $this->info('Completed processing all societe databases.');

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
