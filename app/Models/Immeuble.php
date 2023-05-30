<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Immeuble extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'nom',
        'titre_foncier',
        'projet_id',
        'tranche_id',
        'bloc_id',
    ];
    protected $dates = ['deleted_at'];
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
}
