<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tranche extends Model
{
    use HasFactory;
    protected $fillable = [
        'nom',
        'projet_id',
        'tranche_id',
        'titre_foncier',
        // Add other fillable columns here
    ];

    public function projet()
    {
        return $this->belongsTo(Project::class);
    }
    public function tranche()
    {
        return $this->belongsTo(Tranche::class);
    }
}
