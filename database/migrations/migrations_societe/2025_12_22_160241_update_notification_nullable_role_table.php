<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Enum\RoleEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Build the enum values string for MySQL
        $enumValues = [
            RoleEnum::SUPERADMIN->value,
            RoleEnum::ADMIN->value,
            RoleEnum::COMMERCIAL->value,
            RoleEnum::ADMIN_COMMERCIAL->value,
            RoleEnum::NOTAIRE->value,
            RoleEnum::RESPO_LIVRAISON->value,
            RoleEnum::COMPTABLE->value,
            RoleEnum::SAV->value,
            RoleEnum::RESPO_COMMERCIAL->value,
            RoleEnum::AGENT_ADMINISTRATIF->value
        ];

        $enumString = "'" . implode("', '", $enumValues) . "'";

        // Use raw SQL to modify the enum column to be nullable
        DB::statement("ALTER TABLE notifications MODIFY COLUMN role ENUM({$enumString}) NULL COMMENT '1=>superadmin 2=>Admin 3=>Commercial 4=>admin_commercial 5=>Notaire 6=>RESPO LIVRAISON 7=>Comptable 8=>SAV 9=>RESPO_COMMERCIAL 10=>AGENT_ADMINISTRATIF'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Build the enum values string for MySQL
        $enumValues = [
            RoleEnum::SUPERADMIN->value,
            RoleEnum::ADMIN->value,
            RoleEnum::COMMERCIAL->value,
            RoleEnum::ADMIN_COMMERCIAL->value,
            RoleEnum::NOTAIRE->value,
            RoleEnum::RESPO_LIVRAISON->value,
            RoleEnum::COMPTABLE->value,
            RoleEnum::SAV->value,
            RoleEnum::RESPO_COMMERCIAL->value,
            RoleEnum::AGENT_ADMINISTRATIF->value
        ];

        $enumString = "'" . implode("', '", $enumValues) . "'";

        // Revert back to NOT NULL
        DB::statement("ALTER TABLE notifications MODIFY COLUMN role ENUM({$enumString}) NOT NULL COMMENT '1=>superadmin 2=>Admin 3=>Commercial 4=>admin_commercial 5=>Notaire 6=>RESPO LIVRAISON 7=>Comptable 8=>SAV 9=>RESPO_COMMERCIAL 10=>AGENT_ADMINISTRATIF'");
    }
};
