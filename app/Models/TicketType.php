<?php

namespace AppModels;

use IlluminateDatabaseEloquentModel;
use IlluminateDatabaseEloquentRelationsHasMany;

class TicketType extends Model
{
    protected $fillable = ['name', 'price', 'description', 'status', 'sort'];

    protected $casts = ['price' => 'decimal:2', 'status' => 'boolean'];

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function passengers(): HasMany
    {
        return $this->hasMany(OrderPassenger::class);
    }
}
