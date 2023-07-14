<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Projet extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'projets';
    /* protected $fillable = [
        'nom', 'code', 'adresse',
        'date_autorisation_construction',
        'date_permis_habiter', 'surface_terrain', 'prix_acquisition',
        'limite_annulation_reservation', 'nbre_tranches',
        'nbre_blocs', 'nbre_immeubles', 'nbre_biens', 'type_id',
    ]; */

    protected $dates = ['deleted_at'];

    public function typeprojet()
    {
        return $this->belongsTo(TypeProjet::class, 'type_id');
    }

    public function societe()
    {
        return $this->belongsTo(Societe::class, 'societe_id');
    }
    public function tranche()
    {
        return $this->hasManyany(Tranche::class, 'projet_id');
    }

}
