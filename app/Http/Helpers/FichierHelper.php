<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FichierHelper
{
    /**
     * Check if we are in production environment
     *
     * @return bool
     */
    private static function isProduction()
    {
        return app()->environment('production');
    }

    /**
     * Get the appropriate disk for file operations
     *
     * @return string
     */
    private static function getDisk()
    {
        return self::isProduction() ? 's3' : 'local';
    }

    /**
     * Get the base path for files
     *
     * @return string
     */
    private static function getBasePath()
    {
        if (self::isProduction()) {
            return ''; // S3 doesn't need base path
        }
        return public_path('docs');
    }

    /**
     * Add a file (supports both local and S3)
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $societe
     * @param int $id
     * @param string $doss
     * @param string $nom_file
     * @return string
     */
    public static function ajouter_fichier($file, $societe, $id, $doss, $nom_file)
    {
        $relativePath = $societe . '_' . $id . '/' . $doss;
        $fullRelativePath = $relativePath . '/' . $nom_file;

        if (self::isProduction()) {
            // Production: Upload to S3
            Storage::disk('s3')->putFileAs($relativePath, $file, $nom_file);
        } else {
            // Local: Save to public/docs
            $directory = public_path('docs/' . $relativePath);

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            $file->move($directory, $nom_file);
        }

        return $fullRelativePath;
    }

    /**
     * Delete a file (supports both local and S3)
     *
     * @param string $societe
     * @param int $id
     * @param string $doss
     * @param string $nom_file
     * @return bool
     */
    public static function supprimer_fichier($societe, $id, $doss, $nom_file)
    {
        if (!$nom_file) {
            return true;
        }


        $relativePath = $societe . '_' . $id . '/' . $doss . '/' . $nom_file;

        if (self::isProduction()) {
            // Production: Delete from S3
            if (Storage::disk('s3')->exists($relativePath)) {
                return Storage::disk('s3')->delete($relativePath);
            }
        } else {
            // Local: Delete from public/docs
            $filePath = public_path('docs/' . $relativePath);
            if (File::exists($filePath)) {
                return File::delete($filePath);
            }
        }

        return false;
    }

    /**
     * Get file URL (supports both local and S3)
     *
     * @param string $societe
     * @param int $id
     * @param string $doss
     * @param string $nom_file
     * @return string|null
     */
    public static function get_file_url($societe, $id, $doss, $nom_file)
    {
        if (!$nom_file) {
            return null;
        }

        $relativePath = $societe . '_' . $id . '/' . $doss . '/' . $nom_file;

        if (self::isProduction()) {
            // Production: Get S3 URL
            if (Storage::disk('s3')->exists($relativePath)) {
                return Storage::disk('s3')->url($relativePath);
            }
        } else {
            // Local: Get local URL
            $filePath = public_path('docs/' . $relativePath);
            if (File::exists($filePath)) {
                return asset('docs/' . $relativePath);
            }
        }

        return null;
    }

    /**
     * Check if file exists (supports both local and S3)
     *
     * @param string $societe
     * @param int $id
     * @param string $doss
     * @param string $nom_file
     * @return bool
     */
    public static function fichier_existe($societe, $id, $doss, $nom_file)
    {
        if (!$nom_file) {
            return false;
        }

        $relativePath = $societe . '_' . $id . '/' . $doss . '/' . $nom_file;

        if (self::isProduction()) {
            return Storage::disk('s3')->exists($relativePath);
        } else {
            $filePath = public_path('docs/' . $relativePath);
            return File::exists($filePath);
        }
    }

    /**
     * Rename a societe's folder structure (local only - for S3, you'd need to copy objects)
     *
     * @param string $ancienNom
     * @param string $nouveauNom
     * @param int $societeId
     * @return bool
     */
    public static function renommer_dossier_societe($ancienNom, $nouveauNom, $societeId)
    {
        if (self::isProduction()) {
            // For S3, we need to copy all objects from old prefix to new prefix
            $oldPrefix = $ancienNom . '_' . $societeId . '/';
            $newPrefix = $nouveauNom . '_' . $societeId . '/';

            $files = Storage::disk('s3')->files($oldPrefix);

            foreach ($files as $file) {
                $newFile = str_replace($oldPrefix, $newPrefix, $file);
                Storage::disk('s3')->copy($file, $newFile);
                Storage::disk('s3')->delete($file);
            }

            return true;
        } else {
            // Local: Rename directories
            $ancienDossierBase = $ancienNom . '_' . $societeId;
            $nouveauDossierBase = $nouveauNom . '_' . $societeId;

            $dossiers = ['docs'];

            foreach ($dossiers as $dossierType) {
                $ancienChemin = public_path($dossierType . '/' . $ancienDossierBase);
                $nouveauChemin = public_path($dossierType . '/' . $nouveauDossierBase);

                if (File::exists($ancienChemin)) {
                    File::move($ancienChemin, $nouveauChemin);
                }
            }

            return true;
        }
    }
}
