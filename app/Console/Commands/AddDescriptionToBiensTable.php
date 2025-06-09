<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDescriptionToBiensTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:add-description-to-biens {database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add description column to biens table in the specified tenant database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $database = $this->argument('database');
        
        $this->info("Adding description column to biens table in database: $database");

        // Configure the connection for the tenant database
        config(['database.connections.tenant' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $database,
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ]]);

        try {
            // Check if the description column already exists
            $hasColumn = Schema::connection('tenant')->hasColumn('biens', 'description');
            
            if ($hasColumn) {
                $this->warn("Column description already exists in biens table in $database. Skipping.");
                return 0;
            }
            
            // Add the description column as TEXT
            DB::connection('tenant')->statement('ALTER TABLE `biens` ADD COLUMN `description` TEXT NULL AFTER `titre_foncier`');
            
            $this->info("Successfully added description column to biens table in $database database.");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Failed to add column: " . $e->getMessage());
            return 1;
        }
    }
}
