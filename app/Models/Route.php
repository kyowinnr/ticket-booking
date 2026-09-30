<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    protected $fillable = ['name', 'departure_port', 'arrival_port', 'status', 'sort'];
    protected $casts = ['status' => 'boolean'];

    public function trips(): HasMany { return $this->hasMany(Trip::class); }
}
