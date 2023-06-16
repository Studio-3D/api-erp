<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tranche extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'tranches';
    protected $fillable = [
        'nom',
        'projet_id', 'date_lancement',
        'date_livraison',
        'niveau_etages', 'nbre_blocs','nbre_immeubles',
        'nbre_biens'
    ];
    protected $dates = ['deleted_at'];


    public function projet()
    {
        return $this->belongsTo(Projet::class, 'projet_id');
    }
   
}


