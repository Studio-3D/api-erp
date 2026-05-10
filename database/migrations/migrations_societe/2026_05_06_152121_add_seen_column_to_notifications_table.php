<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
    {
        if (!Schema::hasColumn('notifications', 'seen')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->json('seen')->nullable();
            });
        } else {
            DB::statement("ALTER TABLE notifications MODIFY seen JSON NULL");
        }
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE notifications MODIFY seen TINYINT(1) NOT NULL DEFAULT 0");
    }
    };
