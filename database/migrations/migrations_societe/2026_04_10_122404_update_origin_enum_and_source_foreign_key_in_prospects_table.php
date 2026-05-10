<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the existing foreign key if exists
        $this->dropForeignKeyIfExists('prospects', 'prospects_source_foreign');

        // Modify origin to enum with default value 'manuel'
        DB::statement("ALTER TABLE prospects MODIFY COLUMN origin ENUM('manuel', 'visite', 'whatsapp', 'facebook', 'landingPage', 'import', 'appel') NOT NULL DEFAULT 'manuel'");

        // Make source column nullable using raw SQL (NOT change())
        DB::statement("ALTER TABLE prospects MODIFY COLUMN source BIGINT UNSIGNED NULL");

        // Add foreign key constraint using raw SQL
        DB::statement("ALTER TABLE prospects ADD CONSTRAINT prospects_source_foreign FOREIGN KEY (source) REFERENCES sources(id) ON DELETE CASCADE");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key
        $this->dropForeignKeyIfExists('prospects', 'prospects_source_foreign');

        // Revert origin back to string
        DB::statement("ALTER TABLE prospects MODIFY COLUMN origin VARCHAR(255) NOT NULL");

        // Revert source back to NOT NULL using raw SQL
        DB::statement("ALTER TABLE prospects MODIFY COLUMN source BIGINT UNSIGNED NOT NULL");
    }

    private function dropForeignKeyIfExists($table, $foreignKeyName)
    {
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ", [$table]);

        $foreignKeyNames = array_map(function($fk) {
            return $fk->CONSTRAINT_NAME;
        }, $foreignKeys);

        if (in_array($foreignKeyName, $foreignKeyNames)) {
            DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$foreignKeyName}");
        }
    }
};
