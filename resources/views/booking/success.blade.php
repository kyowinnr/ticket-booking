@extends('layouts.booking')
@section('content')
<div class="card p-4 p-md-5">
<div class="text-center">
<div class="fs-1">✓</div>
<h1 class="h3">訂位完成</h1>
<p class="text-muted">請保存以下訂單編號，後續查詢訂單或櫃台辦理時使用。</p>
<div class="display-6 fw-bold my-4">{{ $order->order_no }}</div>
<div class="badge text-bg-warning">待確認／尚未付款</div>
</div>
<hr class="my-4">

<h2 class="h5">航次</h2>
@foreach($order->items->groupBy(fn($item) => $item->trip_id) as $tripItems)
@php($trip=$tripItems->first()->trip)
<div class="border rounded p-3 mb-3">
<div class="fw-bold">{{ $trip->departure_date->format('Y-m-d') }}　{{ $trip->route->departure_port }} → {{ $trip->route->arrival_port }}</div>
<div class="text-muted mt-1">{{ $trip->departure_time->format('H:i') }} @if($trip->arrival_time) → {{ $trip->arrival_time->format('H:i') }} @endif ・ {{ $trip->ship->name }}</div>
<div class="mt-2">
@foreach($tripItems as $item)
<span class="badge text-bg-light me-1">{{ $item->ticketType->name }} × {{ $item->quantity }}</span>
@endforeach
</div>
</div>
@endforeach

<div class="row g-3 mb-4">
<div class="col-md-6"><strong>訂位人</strong><br>{{ $order->contact_name }} / {{ $order->contact_phone }}</div>
<div class="col-md-6"><strong>總金額</strong><br>NT$ {{ number_format($order->total_amount) }}</div>
</div>

<h2 class="h5">旅客</h2>
@foreach($order->items->groupBy('trip_id') as $tripItems)
@php($trip=$tripItems->first()->trip)
<div class="mb-4">
<div class="fw-semibold mb-2">{{ $trip->route->departure_port }} → {{ $trip->route->arrival_port }}</div>
<div class="table-responsive"><table class="table">
<thead><tr><th>票種</th><th>姓名</th><th>證件號碼</th></tr></thead>
<tbody>
@foreach($tripItems as $item)
@foreach($order->passengers->where('order_item_id',$item->id) as $passenger)
<tr><td>{{ $passenger->ticketType->name }}</td><td>{{ $passenger->name }}</td><td>{{ $passenger->id_number ?: '—' }}</td></tr>
@endforeach
@endforeach
</tbody>
</table></div>
</div>
@endforeach

<div class="text-center mt-4"><a href="{{ route('home') }}" class="btn btn-primary">返回首頁</a></div>
</div>
@endsection
