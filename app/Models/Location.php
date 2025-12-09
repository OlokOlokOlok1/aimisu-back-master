<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = ['name', 'latitude', 'longitude', 'description'];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }


}
