<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'nom',
        'code',
        'adresse',
        'date_autorisation_construction',
        'date_permis_habiter',
        'titre_foncier',
        'surface_terrain',
        'prix_acquisition',
        'limite_annulation_reservation',
        'type_id',
        'nbr_tranches',
        'nbr_blocs',
        'nbr_immeubles',
        'nbr_biens'
    ];
    public function type()
    {
        return $this->belongsTo(TypeProject::class, 'type_id');
    }
}
