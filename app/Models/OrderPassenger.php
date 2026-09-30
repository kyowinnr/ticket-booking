<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class OrderPassenger extends Model {
 protected $fillable=['order_id','ticket_type_id','name','id_number','birthday','phone','gender'];
 protected $casts=['birthday'=>'date'];
 public function order(): BelongsTo{return $this->belongsTo(Order::class);}
 public function ticketType(): BelongsTo{return $this->belongsTo(TicketType::class);}
}