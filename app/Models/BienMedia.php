<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;

class BienMedia extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bien_id',
        'file_path',
        'file_type', // 'image' or 'video'
        'mime_type',
        'original_name',
        'title',
        'description',
        'is_featured',
    ];

    /**
     * Get the bien that owns the media.
     */
    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }

    /**
     * Get the full URL for the media file.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        // Use URL generation with named route for more reliable paths
        return route('media.show', ['path' => $this->file_path]);
    }
}
