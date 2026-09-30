<?php

namespace AppModels;

use IlluminateDatabaseEloquentModel;
use IlluminateDatabaseEloquentRelationsBelongsTo;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'ticket_type_id', 'quantity', 'unit_price', 'subtotal'];

    protected $casts = ['unit_price' => 'decimal:2', 'subtotal' => 'decimal:2'];

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function ticketType(): BelongsTo { return $this->belongsTo(TicketType::class); }
}
