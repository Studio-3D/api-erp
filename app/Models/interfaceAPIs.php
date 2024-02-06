<?php

namespace App\Models;
use SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class interfaceAPIs extends Model
{
    protected $dates=['deleted_at'];
    protected $fillable  = ['client_num','societe_id','source'];
    use HasFactory;
}
