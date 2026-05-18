<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AddAgentAdminRole extends Command
{
    protected $signature = 'role:add-agent-admin';
    protected $description = 'Ajoute le role Agent Admin (10) aux conditions des contrôleurs';

    public function handle()
    {
        $controllersPath = app_path('Http/Controllers/Api/');

        // Vérifier si le dossier existe
        if (!File::exists($controllersPath)) {
            $this->error("Le dossier n'existe pas: " . $controllersPath);
            return 1;
        }

        $files = File::files($controllersPath);
        $modifiedCount = 0;

        $patterns = [
            // Fonctions de base
            '/(RoleHelper::AdminSup\(\))/' => 'RoleHelper::AdminSup() || RoleHelper::AgentAdmin()',
            '/(RoleHelper::ACSup\(\))/' => 'RoleHelper::ACSup() || RoleHelper::AgentAdmin()',
            '/(RoleHelper::AC\(\))/' => 'RoleHelper::AC() || RoleHelper::AgentAdmin()',

            // Fonctions avec RC (Respo Commercial)
            '/(RoleHelper::AdminSup_RC\(\))/' => 'RoleHelper::AdminSup_RC() || RoleHelper::AgentAdmin()',
            '/(RoleHelper::ACSup_RC\(\))/' => 'RoleHelper::ACSup_RC() || RoleHelper::AgentAdmin()',

            // Fonctions Comptable
            '/(RoleHelper::AdminComptable\(\))/' => 'RoleHelper::AdminComptable() || RoleHelper::AgentAdmin()',
            '/(RoleHelper::AdminComptableSup\(\))/' => 'RoleHelper::AdminComptableSup() || RoleHelper::AgentAdmin()',

            // Autres fonctions utiles
            '/(RoleHelper::Notaire_Respo_Comptable_SAV_Comm_RC\(\))/' => 'RoleHelper::Notaire_Respo_Comptable_SAV_Comm_RC() || RoleHelper::AgentAdmin()',
            '/(RoleHelper::AdminSavSup\(\))/' => 'RoleHelper::AdminSavSup() || RoleHelper::AgentAdmin()',
        ];

        foreach ($files as $file) {
            $content = File::get($file);
            $originalContent = $content;

            foreach ($patterns as $pattern => $replacement) {
                $content = preg_replace($pattern, $replacement, $content);
            }

            if ($content !== $originalContent) {
                File::put($file, $content);
                $this->info("✅ Modifié: " . $file->getFilename());
                $modifiedCount++;
            }
        }

        $this->newLine();
        $this->info("🎉 Terminé! $modifiedCount fichier(s) modifié(s).");
        $this->newLine();
        $this->warn("⚠️  N'oubliez pas d'ajouter la méthode AgentAdmin() dans RoleHelper:");
        $this->line("");
        $this->line("    public static function AgentAdmin()");
        $this->line("    {");
        $this->line("        return Auth::guard('api')->check() && Auth::guard('api')->user()->role == 10;");
        $this->line("    }");
        $this->line("");

        return 0;
    }
}
