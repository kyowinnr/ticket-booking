<?php

namespace AppModels;

use IlluminateDatabaseEloquentModel;
use IlluminateDatabaseEloquentRelationsBelongsTo;

class OrderPassenger extends Model
{
    protected $fillable = [
        'order_id', 'ticket_type_id', 'name', 'id_number',
        'birthday', 'phone', 'gender'
    ];

    protected $casts = ['birthday' => 'date'];

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function ticketType(): BelongsTo { return $this->belongsTo(TicketType::class); }
}
