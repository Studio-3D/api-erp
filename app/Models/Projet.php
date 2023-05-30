<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Projet extends Model
{
    use HasFactory, SoftDeletes;
       protected $table = 'projets';

    protected $dates = ['deleted_at'];
    public function typeprojet()
    {
        return $this->HasMany(TypeProjet::class,);
    }
}
