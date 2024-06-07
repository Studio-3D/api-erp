<?php

namespace App\Utils;

use Illuminate\Http\UploadedFile;

class FileManager
{
    private const UPLOADS_PATH = 'uploads';
    public const LOGO_TYPE = 'LOGO';
    public const AVATAR_TYPE = 'AVATAR';

    /**
     * Sauvegarde un fichier dans le répertoire spécifié.
     *
     * @param UploadedFile $file Le fichier à sauvegarder.
     * @param string|null $path Le chemin du répertoire où sauvegarder le fichier.
     * @throws \InvalidArgumentException Si le fichier n'est pas une instance de UploadedFile.
     * @throws \RuntimeException Si le fichier ne peut pas être déplacé.
     * @return string Le nom du fichier sauvegardé.
     */
    public static function save(UploadedFile $file, ?string $path = null): string
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
     * Sauvegarde un fichier dans un sous-répertoire du répertoire spécifié, en fonction du type de fichier.
     *
     * @param UploadedFile $file Le fichier à sauvegarder.
     * @param string $uploadsSubPath Le sous-répertoire où sauvegarder le fichier.
     * @param string $type Le type de fichier (AVATAR_TYPE, LOGO_TYPE, etc.).
     * @throws \InvalidArgumentException Si le type de fichier n'est pas pris en charge.
     * @throws \RuntimeException Si le fichier ne peut pas être déplacé.
     * @return string Le nom du fichier sauvegardé.
     */
    public static function saveOwned(UploadedFile $file, string $uploadsSubPath, string $type): string
    {
        $path = self::UPLOADS_PATH . '/' . $uploadsSubPath;
        switch ($type) {
            case self::AVATAR_TYPE:
                $path .= '/avatars';
                break;
            case self::LOGO_TYPE:
                $path .= '/logos';
                break;
            default:
                throw new \InvalidArgumentException("Type de fichier non pris en charge : $type");
            // Ajouter autant de types que vous souhaitez
            //pour d'autres types de fichiers (utilisateurs, projets, biens...)
        }
        return self::save($file, $path);
    }
}
