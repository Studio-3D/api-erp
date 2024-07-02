<?php
namespace App\Repositories\V1;

use App\Models\V1\Societe;
use App\Repositories\V1\Contracts\SocieteRepository;
use App\Traits\PaginatesResults;

class SocieteRepositoryDefault implements SocieteRepository
{
    use PaginatesResults;
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
    function all(array $filters, int $size, int $page)
    {
        $query = Societe::query();
        $this->applyFilters($query, $filters);
        return $this->paginateResults($query, $size, $page);
    }
    public function applyFilters($query, array $filters): void
    {
        foreach ($filters as $field => $value) {
            switch ($field) {
                case 'raison_sociale':
                    $query->where('raison_sociale', 'like', '%' . $value . '%');
                    break;
                case 'nom_contact':
                    $query->where('nom_contact', 'like', '%' . $value . '%');
                    break;
                case 'prenom_contact':
                    $query->where('prenom_contact', 'like', '%' . $value . '%');
                    break;
                case 'email':
                    $query->where('email', 'like', '%' . $value . '%');
                    break;
                case 'tel':
                    $query->where('tel', 'like', '%' . $value . '%');
                    break;
                default:
                    break;
            }
        }
    }
    public function paginateResults($query, int $size, int $page): array
    {
        return $this->paginate($query, $size, $page);
    }
}