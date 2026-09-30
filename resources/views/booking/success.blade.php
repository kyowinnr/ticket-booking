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
<div class="row g-3">
<div class="col-md-6"><strong>航次</strong><br>{{ $order->trip->departure_date->format('Y-m-d') }} {{ $order->trip->departure_time->format('H:i') }}</div>
<div class="col-md-6"><strong>航線</strong><br>{{ $order->trip->route->departure_port }} → {{ $order->trip->route->arrival_port }}</div>
<div class="col-md-6"><strong>訂位人</strong><br>{{ $order->contact_name }} / {{ $order->contact_phone }}</div>
<div class="col-md-6"><strong>金額</strong><br>NT$ {{ number_format($order->total_amount) }}</div>
</div>
<h2 class="h5 mt-4">旅客</h2>
<div class="table-responsive"><table class="table">
<thead><tr><th>票種</th><th>姓名</th><th>證件號碼</th></tr></thead>
<tbody>@foreach($order->passengers as $passenger)<tr><td>{{ $passenger->ticketType->name }}</td><td>{{ $passenger->name }}</td><td>{{ $passenger->id_number ?: '—' }}</td></tr>@endforeach</tbody>
</table></div>
<div class="text-center mt-4"><a href="{{ route('home') }}" class="btn btn-primary">返回首頁</a></div>
</div>
@endsection
