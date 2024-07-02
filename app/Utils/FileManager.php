<?php

namespace App\Utils;

use Illuminate\Http\UploadedFile;

class FileManager
{
    private const UPLOADS_PATH = 'uploads';

    /**
     * Fonction utilitaire qui sauvegarde un fichier dans le répertoire spécifié.
     *
     * @param UploadedFile $file Le fichier à sauvegarder.
     * @param string|null $path Le chemin du répertoire où sauvegarder le fichier.
     * @throws \InvalidArgumentException Si le fichier n'est pas une instance de UploadedFile.
     * @throws \RuntimeException Si le fichier ne peut pas être déplacé.
     * @return string Le nom du fichier sauvegardé.
     */
    private static function save(UploadedFile $file, ?string $path = null): string
    {
        $path = $path ?? self::UPLOADS_PATH;
        if (!$file instanceof UploadedFile) {
            throw new \InvalidArgumentException("Le fichier doit être une instance de UploadedFile.");
        }
        $fileName = time() . '.' . $file->extension();
        $file->move(public_path($path), $fileName);
        return $fileName;
    }

    /**
     * Sauvegarde un fichier dans un sous-répertoire du répertoire spécifié
     *
     * @param UploadedFile $file Le fichier à sauvegarder.
     * @param string $uploadsSubPath Le sous-répertoire où sauvegarder le fichier.
     * @return string Le nom du fichier sauvegardé.
     */
    public static function saveFile(UploadedFile $file, string $uploadsSubPath): string
    {
        $path = self::UPLOADS_PATH . '/' . $uploadsSubPath;
        return self::save($file, $path);
    }
}
