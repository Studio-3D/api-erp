<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeBienAppel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'type_biens_appels';
    protected $dates = ['deleted_at'];
    protected $with = ['typeBien'];


    public function traie_appel()
    {
        return $this->belongsTo(TraitementAppel::class, 'traite_appel_id');
    }

    public function typeBien()
    {
        return $this->belongsTo(TypeBien::class, 'type_bien_id');
    }
}
