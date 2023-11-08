<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table='notifications';
    protected $dates=['deleted_at'];

    public function visite(){
        return $this->belongsTo(Visite::class,'visite_id');
    }
    public function prospect(){
        return $this->belongsTo(Prospect::class,'prospect_id');
    }

}
