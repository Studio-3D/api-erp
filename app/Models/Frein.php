<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Frein extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table='freins';
    protected $dates=['deleted_at'];
    protected $with=['freinTranche','FreinEtage','FreinOrientation','FreinTypologie','FreinVue'];

    public function  tranche()
    {
        return $this->belongsToMany(Tranche::class,'frein_tranches');
    }
    public function  typologie(){
        return $this->belongsToMany(Typologie::class,'frein_typologies');
    }
    public function  vue(){
        return $this->belongsToMany(Vue::class,'frein_vues');
    }
    public function  visite(){
        return $this->belongsTo(Visite::class,'visite_id');
    }
    public function  traite_appel(){
        return $this->belongsTo(TraitementAppel::class,'traite_appel_id');
    }
    public function freinTranche()
    {
        return $this->hasMany(FreinTranche::class,'frein_id');
    }
    public function FreinEtage()
    {
        return $this->hasMany(FreinEtage::class,'frein_id');
    }
    public function FreinOrientation()
    {
        return $this->hasMany(FreinOrientation::class,'frein_id');
    }
    public function FreinTypologie()
    {
        return $this->hasMany(FreinTypologie::class,'frein_id');
    }
    public function FreinVue()
    {
        return $this->hasMany(FreinVue::class,'frein_id');
    }
}
