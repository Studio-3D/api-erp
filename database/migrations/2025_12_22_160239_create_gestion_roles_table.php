<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Enum\RoleEnum;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gestion_roles', function (Blueprint $table) {
            $table->id();
            $table->enum('role', [
                RoleEnum::ADMIN->value, // Add ADMIN to the enum list
                RoleEnum::COMMERCIAL->value,
                RoleEnum::NOTAIRE->value,
                RoleEnum::RESPO_LIVRAISON->value,
                RoleEnum::COMPTABLE->value,
                RoleEnum::SAV->value,
                RoleEnum::RESPO_COMMERCIAL->value,
                RoleEnum::AGENT_ADMINISTRATIF->value
            ])->comment('2=>Admin 3=>Commercial 5=>Notaire 6=>RESPO LIVRAISON 7=>Comptable 8=>SAV 9==>RESPO_COMMERCIAL 10==>AGENT_ADMINISTRATIF');
            $table->boolean('projet')->default(false);
            $table->boolean('vente')->default(false);
            $table->boolean('crm')->default(false);
            $table->boolean('remise_cles')->default(false);
            $table->boolean('comptabilite')->default(false);
            $table->boolean('encaissements')->default(false);
            $table->boolean('sav')->default(false);
            $table->boolean('reclamations')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // Insérer les rôles par défaut
        DB::table('gestion_roles')->insert([
            [
                'role' => '2',
                'projet' => true,
                'vente' => true,
                'crm' => true,
                'remise_cles' => true,
                'comptabilite' => true,
                'encaissements' => true,
                'sav' => true,
                'reclamations' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role' => '3',
                'projet' => true,
                'vente' => true,
                'crm' => true,
                'remise_cles' => false,
                'comptabilite' => false,
                'encaissements' => false,
                'sav' => false,
                'reclamations' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('gestion_roles');
    }
};
