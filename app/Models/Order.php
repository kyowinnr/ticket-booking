<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = ['order_no','trip_id','contact_name','contact_phone','contact_email','total_amount','payment_status','status','note'];
    protected $casts = ['total_amount'=>'decimal:2'];
    public function trip(): BelongsTo { return $this->belongsTo(Trip::class); }
    public function items(): HasMany { return $this->hasMany(OrderItem::class); }
    public function passengers(): HasMany { return $this->hasMany(OrderPassenger::class); }
}
