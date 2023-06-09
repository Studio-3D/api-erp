<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DatabaseHelper
{
    public function createNewClientDatabase($raison_sociale, $societe_id)
    {
        
        $databaseName='Erp_'.$raison_sociale.'_'.$societe_id;
        
        if ($this->databaseExists($databaseName)) {
            return response()->json(['message' => 'Database already exists.']);
        }

        DB::statement("CREATE DATABASE IF NOT EXISTS $databaseName");

        $connection = [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $databaseName,
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ];

        $migration = $this->runMigrations($connection);

        if ($migration === true) {
            return response()->json(['message' => 'Database created and migrations ran successfully.']);
        } else {
            return response()->json(['message' => 'Error running migrations.']);
        }
    }

    public function runMigrations($connection)
    {
        config(['database.connections.temp' => $connection]);

        $migration = Artisan::call('migrate', [
            '--database' => 'temp',
            '--path' => 'database/migrations_societe',
        ]);

        config(['database.connections.temp' => null]);

        return $migration === 0;
    }

    public function databaseExists($databaseName)
    {
        $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$databaseName'";
        $database = DB::select($query);

        return count($database) > 0;
    }
}
