<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeBien extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'projet_id',
    ];
    public function projet()
    {
        return $this->belongsTo(Project::class);
    }
}
