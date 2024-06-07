<?php
namespace App\Repositories\V1;

use App\Models\V1\Societe;
use App\Repositories\V1\SocieteRepository;
use Illuminate\Support\Facades\Log;

class SocieteRepositoryDefault implements SocieteRepository
{
    public function create(array $data)
    {
        return Societe::create($data);
    }
    public function update($id, array $data)
    {
        $societe = Societe::findOrFail($id);
        $query = $societe->newQuery()->whereKey($id);
        $query->update($data);
        return $societe;
    }
    public function find($id)
    {
        return Societe::findOrFail($id);
    }
}