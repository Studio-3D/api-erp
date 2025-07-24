<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebhookEvent extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'webhook_events';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'platform', 
        'event_type', // Changed from 'type' to 'event_type'
        'data', 
        'page_id', 
        'processed', 
        'processing_notes'
    ];

    protected $casts = [
        'data' => 'array',
        'processed' => 'boolean',
    ];

    // Scope for processed events
    public function scopeProcessed($query)
    {
        return $query->where('processed', true);
    }

    // Scope for unprocessed events
    public function scopeUnprocessed($query)
    {
        return $query->where('processed', false);
    }

    // Scope for specific platform
    public function scopePlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    // Scope for specific page
    public function scopePage($query, $pageId)
    {
        return $query->where('page_id', $pageId);
    }
}
