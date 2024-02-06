<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacebookMessage extends Model
{
    use HasFactory;
    protected $fillable = ['sender_id', 'user_name', 'user_email', 'message'];
}
