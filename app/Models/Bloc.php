<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Bloc extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'nom',
        'projet_id',
        'tranche_id',
        'titre_foncier',
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
}
