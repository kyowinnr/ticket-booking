<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketType extends Model
{
    protected $fillable = ['name', 'price', 'description', 'status', 'sort'];
    protected $casts = ['price' => 'decimal:2', 'status' => 'boolean'];

    public function orderItems(): HasMany { return $this->hasMany(OrderItem::class); }
    public function passengers(): HasMany { return $this->hasMany(OrderPassenger::class); }
}
