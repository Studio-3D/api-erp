<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bien extends Model
{

    use HasFactory, SoftDeletes;
    protected $fillable = [
        'propriete_dite_bien',
        'numero',
        'niveau',
        'type_id',
        'orientation',
        'conventionne',
        'prix_unitaire',
        'prix',
        'superficie_architecte',
        'superficie_habitable',
        'nbre_facades',
        'superficie_parking',
        'superficie_box',
        'superficie_terrasse',
        'superficie_jardin',
        'titre_foncier',
        'etat',
        'projet_id',
        'tranche_id',
        'bloc_id',
        'immeuble_id',
    ];
    protected $dates = ['deleted_at'];

    public function TypeBien()
    {
        return $this->belongsTo(TypeBien::class, 'type_id');
    }

    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    public function tranche()
    {
        return $this->belongsTo(Tranche::class);
    }

    public function bloc()
    {
        return $this->belongsTo(Bloc::class);
    }

    public function immeuble()
    {
        return $this->belongsTo(Immeuble::class);
    }
}
