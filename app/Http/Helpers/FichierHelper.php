<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FichierHelper
{
    /**
     * Vérifier si on est sur Cloudways
     */
    private static function isCloudways()
    {
        return (isset($_SERVER['cw_allowed_ip']) ||
                file_exists('/home/master/.cloudways') ||
                getenv('CLOUDWAYS_APP_NAME') !== false ||
                strpos(__DIR__, '/home/1633242.cloudwaysapps.com/') !== false);
    }

    /**
     * Obtenir le chemin de base pour les fichiers
     */
    private static function getBasePath()
    {
        if (self::isCloudways()) {
            // Chemin correct sur Cloudways
            return base_path('public_html/docs');
        }

        // Chemin local
        return public_path('docs');
    }

    /**
     * Ajouter un fichier
     */
    public static function ajouter_fichier($file, $societe, $id, $doss, $nom_file)
    {
        $relativePath = $societe . '_' . $id . '/' . $doss;
        $directory = self::getBasePath() . '/' . $relativePath;

        // Créer le dossier s'il n'existe pas
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Déplacer le fichier
        $file->move($directory, $nom_file);

        // Corriger les permissions
        if (self::isCloudways()) {
            chmod($directory . '/' . $nom_file, 0644);
        }

        // Retourner le chemin relatif pour stockage en base
        return $nom_file;
    }

    /**
     * Récupérer l'URL d'un fichier
     */
    public static function get_file_url($societe, $id, $doss, $nom_file)
    {
        if (!$nom_file) {
            return null;
        }

        // Construire l'URL publique
        if (self::isCloudways()) {
            // Sur Cloudways, utiliser asset() qui pointe vers public_html
            return asset('docs/' . $societe . '_' . $id . '/' . $doss . '/' . $nom_file);
        }

        // En local
        return asset('docs/' . $societe . '_' . $id . '/' . $doss . '/' . $nom_file);
    }
}
