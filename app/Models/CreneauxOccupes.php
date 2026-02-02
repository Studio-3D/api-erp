<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreneauxOccupes extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table='creneaux_occupes';
    protected $dates=['deleted_at'];
     // Spécifiez les colonnes qui peuvent être remplies en masse
    protected $fillable = [
        'user_id', // AJOUTEZ CE CHAMP
        'debut',
        'fin',
        'disponible',
        'type',
        'created_at',
        'updated_at'
    ];

    // Spécifiez les casts de type
    protected $casts = [
        'debut' => 'datetime',
        'fin' => 'datetime',
        'disponible' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id')->withTrashed();
    }
     public function reservation()
    {
        return $this->belongsTo(Reservation::class,'reservation_id');
    }
}
