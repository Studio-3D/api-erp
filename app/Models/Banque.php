<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banque extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table='banques';
    protected $dates=['deleted_at'];
    public function avance()
    {
        return $this->hasMany(Avance::class);
    }
    public function HistoriqueAvance()
    {
        return $this->hasMany(HistoriqueAvance::class);
    }
    public function desistements()
    {
        return $this->hasMany(Desistement::class);
    }
    public function penalite_desistements()
    {
        return $this->hasMany(PenaliteDesistement::class);
    }
    public function remboursements()
    {
        return $this->hasMany(Remboursement::class);
    }
    public function factures()
    {
        return $this->hasMany(Facture::class);
    }
    public function credits()
    {
        return $this->hasMany(Credit::class);
    }
}
