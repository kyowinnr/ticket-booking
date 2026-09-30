@extends('layouts.booking')
@section('content')
<div class="mb-4">
<h1 class="h3 mb-2">選擇航次</h1>
<div class="text-muted">{{ $date }} ・ {{ $direction === 'outbound' ? '布袋港 → 澎湖' : '澎湖 → 布袋港' }}</div>
</div>

@if(!$route)
<div class="card p-4"><div class="alert alert-warning mb-0">目前尚未建立符合方向的航線資料，請先到管理後台建立航線。</div></div>
@elseif($trips->isEmpty())
<div class="card p-4"><div class="alert alert-info mb-0">這一天目前沒有可訂航次。</div></div>
@else
<div class="row g-3">
@foreach($trips as $trip)
<div class="col-12">
<div class="card p-4">
<div class="row align-items-center">
<div class="col-md-7">
<div class="text-muted small">{{ $trip->route->name }}</div>
<div class="fs-4 fw-bold">{{ substr($trip->departure_time->format('H:i'),0,5) }} @if($trip->arrival_time) → {{ substr($trip->arrival_time->format('H:i'),0,5) }} @endif</div>
<div class="mt-2">船舶：{{ $trip->ship->name }} ・ 剩餘 {{ $trip->available_seats }} 位</div>
</div>
<div class="col-md-5 text-md-end mt-3 mt-md-0">
@if($trip->available_seats > 0)
<a href="{{ route('booking.create',$trip) }}" class="btn btn-primary px-4">選擇此航次</a>
@else
<span class="badge text-bg-secondary">已額滿</span>
@endif
</div>
</div>
</div>
</div>
@endforeach
</div>
@endif
@endsection
