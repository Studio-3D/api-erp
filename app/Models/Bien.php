<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bien extends Model
{

    use HasFactory, SoftDeletes;
    protected $table = 'biens';
    protected $dates = ['deleted_at'];

    public function typebien()
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
