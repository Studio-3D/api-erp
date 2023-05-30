<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeProjet extends Model
{
    use HasFactory, SoftDeletes;
           protected $table = 'type_projets';

  
    protected $dates = ['deleted_at'];
    public function projet()
    {
        return $this->HasMany(Projet::class);
    }
   
}
