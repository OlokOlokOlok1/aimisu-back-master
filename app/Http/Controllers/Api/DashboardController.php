<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'category',
        'start_date',
        'end_date',
        'daily_times',
        'location_id',
        'organization_id',
        'created_by',
        'status',
        'rejection_reason',
        'published_at',
    ];

    protected $casts = [
        'daily_times' => 'array',
        'published_at' => 'datetime',
    ];

    // ✅ ADD THIS SCOPE - fixes your error!
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }
}
