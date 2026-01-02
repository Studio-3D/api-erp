<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatutClient extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table='statut_clients';
    protected $with = ['reservation','user','avance'];
    protected $dates=['deleted_at'];

     public function client(){
        return $this->belongsTo(Client::class,'client_id');
    }
    public function reservation(){
        return $this->belongsTo(Reservation::class,'reservation_id');
    }
    public function avance(){
        return $this->belongsTo(Avance::class,'avance_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id_traite')->withTrashed();
    }
    public function visite(){
            return $this->belongsTo(Visite::class,'visite_id');
    }
    public function desistement(){
            return $this->belongsTo(Desistement::class,'desistement_id');
    }
    public function penalite(){
            return $this->belongsTo(Penalite::class,'penalite_id');
    }
     public function remboursement(){
            return $this->belongsTo(Remboursement::class,'remboursement_id');
    }
     public function rdv(){
            return $this->belongsTo(Rendez_vous::class,'rdv_id');
    }
}
