<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Societe extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'societes';
    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->hasMany(User::class, 'user_id');
    }
}
