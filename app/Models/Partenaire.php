<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partenaire extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'partenaires';
    protected $dates = ['deleted_at'];

    public function projet()
    {
        return $this->belongsTo(Projet::class, 'projet_id');
    }
    public function client()
    {
        return $this->hasMany(Client::class);
    }

    public function prospect()
    {
        return $this->hasMany(Prospect::class, 'partenaire_id');
    }

}
