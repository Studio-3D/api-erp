<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rendez_vous extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table='rendez_vous';
    protected $dates=['deleted_at'];
    protected $with=['user','reservation'];
    protected $casts = [
            'relances_history' => 'array',
            'rdv' => 'datetime',
            'prochaine_relance' => 'datetime',
            'date_validation' => 'datetime',
        ];
    public function  reservation()
    {
        return $this->belongsTo(Reservation::class,'reservation_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id')->withTrashed();
    }

}
