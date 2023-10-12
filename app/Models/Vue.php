<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vue extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table='vues';
    protected $dates=['deleted_at'];

    public function  frein(){
        return $this->belongsToMany(Frein::class,'frein_vues');
    }

    public function  projet(){
        return $this->belongsTo(Projet::class,'projet_id');
    }
    public function bien()
    {
        return $this->hasMany(Bien::class);
    }
}
