<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to modify the enum column
        DB::statement("ALTER TABLE imports MODIFY COLUMN statut ENUM('0', '1', '2', '3') NOT NULL COMMENT '0=>en_attente 1=>en_cours 2=>importe 3=>echoue'");

        // Add type column using Schema builder since it's a new column
        Schema::table('imports', function (Blueprint $table) {
            $table->enum('type', ['0', '1', '2', '3'])
                  ->nullable()
                  ->comment('0=>creer bien, 1=>modif en masse, 2=>titre foncier, 3=>prospects')
                  ->default('0');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert statut column back using raw SQL
        DB::statement("ALTER TABLE imports MODIFY COLUMN statut ENUM('0', '1', '2') NOT NULL COMMENT '0=>en cours 1=>success 2=>echoué'");

        // Drop the type column
        Schema::table('imports', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
