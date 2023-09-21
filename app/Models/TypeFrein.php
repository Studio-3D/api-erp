<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeFrein extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'type_freins';
    protected $fillable = [
        'description'
    ];
    protected $dates = ['deleted_at'];
   
}
