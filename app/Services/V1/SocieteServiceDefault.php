<?php
namespace App\Services\V1;

use App\Services\V1\Contracts\SocieteService;
use App\Events\NewSocieteEvent;
use App\Http\Helpers\DatabaseHelper;
use App\Repositories\V1\Contracts\SocieteRepository;
use Illuminate\Support\Facades\Config;
use App\Utils\FileManager;

class SocieteServiceDefault implements SocieteService
{
    private $societeRepository;
    private $databaseHelper;
    public function __construct(SocieteRepository $societeRepository)
    {
        $this->societeRepository = $societeRepository;
        $this->databaseHelper = new DatabaseHelper(); // This is bad, unnecessary coupling
        //TODO: Define static methods in DatabaseHelper class instead
    }
    public function createSociete(array $data)
    {
        $raison_sociale_concatene = str_replace(' ', '', $data['raison_sociale']);
        $data['raison_sociale_concatene'] = $raison_sociale_concatene;

        $societe = $this->societeRepository->create($data);

        $file = $data['logo'];
        if (isset($file)) {
            $societeName = $raison_sociale_concatene . '_' . $societe->id;
            $fileName = FileManager::saveFile($file, "$societeName/logos");
            $societe->logo = $fileName;
            $societe = $this->societeRepository->update($societe->id, ['logo' => $societe->logo]);
        }

        $response = $this->databaseHelper->createNewClientDatabase($raison_sociale_concatene, $societe->id);

        Config::set('broadcasting.default', 'pusher_1');
        broadcast(new NewSocieteEvent($societe->id));

        return $response;
    }

    public function getSocieteById(int $id)
    {
        return $this->societeRepository->find($id);
    }

    public function getSocietes(array $filters, int $size, int $page)
    {
        return $this->societeRepository->all($filters, $size, $page);
    }
}
