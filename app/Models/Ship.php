<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ship extends Model
{
    protected $fillable = ['name', 'capacity', 'status', 'description'];
    protected $casts = ['status' => 'boolean'];

    public function trips(): HasMany { return $this->hasMany(Trip::class); }
}
