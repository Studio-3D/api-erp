<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateBienMediaTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create-bien-media-table {database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create bien_media table in the specified tenant database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $database = $this->argument('database');
        
        $this->info("Creating bien_media table in database: $database");

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

        // Check if the table already exists in the tenant database
        if (Schema::connection('tenant')->hasTable('bien_media')) {
            $this->warn("Table bien_media already exists in $database. Skipping creation.");
            return 0;
        }

        // Run the SQL to create the table
        try {
            DB::connection('tenant')->statement('
                CREATE TABLE `bien_media` (
                    `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `bien_id` bigint unsigned NOT NULL,
                    `file_path` varchar(255) NOT NULL,
                    `file_type` varchar(255) NOT NULL,
                    `mime_type` varchar(255) NOT NULL,
                    `original_name` varchar(255) NOT NULL,
                    `title` varchar(255) NULL,
                    `description` text NULL,
                    `is_featured` tinyint(1) NOT NULL DEFAULT \'0\',
                    `created_at` timestamp NULL,
                    `updated_at` timestamp NULL,
                    `deleted_at` timestamp NULL
                ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
            ');
            
            // Add foreign key constraint
            DB::connection('tenant')->statement('
                ALTER TABLE `bien_media`
                ADD CONSTRAINT `bien_media_bien_id_foreign`
                FOREIGN KEY (`bien_id`)
                REFERENCES `biens` (`id`)
                ON DELETE CASCADE;
            ');
            
            $this->info("Successfully created bien_media table in $database database.");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Failed to create table: " . $e->getMessage());
            return 1;
        }
    }
}
