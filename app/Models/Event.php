<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'department_id',
        'organization_id',
        'created_by',
        'status',
        'rejection_reason',
        'published_at',
    ];

    protected $casts = [
        'daily_times'  => 'json',
        'start_date'   => 'date',
        'end_date'     => 'date',
        'published_at' => 'datetime',
    ];

    protected $with = ['organization', 'createdBy', 'location'];

    protected $appends = ['registrations_count'];

    public function getRegistrationsCountAttribute(): int
    {
        return $this->registrations()->count();
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
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

    public function getStartDateTime($date)
    {
        $dateStr = $date->format('Y-m-d');
        if (isset($this->daily_times[$dateStr])) {
            [$start, $end] = explode('-', $this->daily_times[$dateStr]);
            return $date->format('Y-m-d') . ' ' . $start;
        }
        return null;
    }

    public function getEndDateTime($date)
    {
        $dateStr = $date->format('Y-m-d');
        if (isset($this->daily_times[$dateStr])) {
            [$start, $end] = explode('-', $this->daily_times[$dateStr]);
            return $date->format('Y-m-d') . ' ' . $end;
        }
        return null;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
