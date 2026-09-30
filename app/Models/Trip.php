<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Trip extends Model {
 protected $fillable=['route_id','ship_id','departure_date','departure_time','arrival_time','capacity','booked_count','status','note'];
 protected $casts=['departure_date'=>'date','departure_time'=>'datetime:H:i','arrival_time'=>'datetime:H:i'];
 public function route(): BelongsTo{return $this->belongsTo(Route::class);}
 public function ship(): BelongsTo{return $this->belongsTo(Ship::class);}
 public function orders(): HasMany{return $this->hasMany(Order::class);}
 public function getAvailableSeatsAttribute(): int{return max(0,$this->capacity-$this->booked_count);}
}