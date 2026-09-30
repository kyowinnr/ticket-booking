<?php

namespace AppModels;

use IlluminateDatabaseEloquentModel;
use IlluminateDatabaseEloquentRelationsHasMany;

class Ship extends Model
{
    protected $fillable = ['name', 'capacity', 'status', 'description'];

    protected $casts = ['status' => 'boolean'];

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }
}
