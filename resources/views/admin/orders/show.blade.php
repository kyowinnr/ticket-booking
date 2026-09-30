@extends('layouts.admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
<div><h1 class="h3 mb-1">訂單 {{ $order->order_no }}</h1><div class="text-muted">建立時間：{{ $order->created_at->format('Y-m-d H:i:s') }}</div></div>
<a class="btn btn-outline-secondary" href="{{ route('admin.orders.index') }}">返回訂單列表</a>
</div>

<div class="row g-4">
<div class="col-lg-8">
<div class="card p-4 mb-4">
<h2 class="h5 mb-3">航次資訊</h2>
<div class="row g-3">
<div class="col-md-6"><span class="text-muted">日期</span><br>{{ $order->trip->departure_date->format('Y-m-d') }}</div>
<div class="col-md-6"><span class="text-muted">時間</span><br>{{ $order->trip->departure_time->format('H:i') }} @if($order->trip->arrival_time)～ {{ $order->trip->arrival_time->format('H:i') }} @endif</div>
<div class="col-md-6"><span class="text-muted">航線</span><br>{{ $order->trip->route->departure_port }} → {{ $order->trip->route->arrival_port }}</div>
<div class="col-md-6"><span class="text-muted">船舶</span><br>{{ $order->trip->ship->name }}</div>
</div>
</div>

<div class="card p-4 mb-4">
<h2 class="h5 mb-3">訂位人</h2>
<div class="row g-3">
<div class="col-md-4"><span class="text-muted">姓名</span><br>{{ $order->contact_name }}</div>
<div class="col-md-4"><span class="text-muted">手機</span><br>{{ $order->contact_phone }}</div>
<div class="col-md-4"><span class="text-muted">Email</span><br>{{ $order->contact_email ?: '—' }}</div>
</div>
</div>

<div class="card p-4">
<h2 class="h5 mb-3">旅客資料</h2>
<div class="table-responsive"><table class="table align-middle">
<thead><tr><th>#</th><th>票種</th><th>姓名</th><th>證件號碼</th><th>生日</th><th>手機</th></tr></thead>
<tbody>@foreach($order->passengers as $i=>$p)<tr><td>{{ $i+1 }}</td><td>{{ $p->ticketType->name }}</td><td>{{ $p->name }}</td><td>{{ $p->id_number ?: '—' }}</td><td>{{ $p->birthday ? $p->birthday->format('Y-m-d') : '—' }}</td><td>{{ $p->phone ?: '—' }}</td></tr>@endforeach</tbody>
</table></div>
</div>
</div>

<div class="col-lg-4">
<div class="card p-4 mb-4">
<h2 class="h5">訂單摘要</h2>
<div class="d-flex justify-content-between mt-3"><span>旅客人數</span><strong>{{ $order->passengers->count() }}</strong></div>
<div class="d-flex justify-content-between mt-2"><span>訂單金額</span><strong>NT$ {{ number_format($order->total_amount) }}</strong></div>
<hr>
@foreach($order->items as $item)<div class="d-flex justify-content-between small mb-2"><span>{{ $item->ticketType->name }} × {{ $item->quantity }}</span><span>NT$ {{ number_format($item->subtotal) }}</span></div>@endforeach
</div>

<div class="card p-4">
<h2 class="h5 mb-3">更新狀態</h2>
<form method="POST" action="{{ route('admin.orders.update',$order) }}">
@csrf @method('PUT')
<label class="form-label">訂單狀態</label>
<select name="status" class="form-select mb-3">
<option value="pending" @selected($order->status==='pending')>待確認</option>
<option value="confirmed" @selected($order->status==='confirmed')>已確認</option>
<option value="cancelled" @selected($order->status==='cancelled')>已取消</option>
<option value="completed" @selected($order->status==='completed')>已完成</option>
</select>
<label class="form-label">付款狀態</label>
<select name="payment_status" class="form-select mb-3">
<option value="unpaid" @selected($order->payment_status==='unpaid')>未付款</option>
<option value="paid" @selected($order->payment_status==='paid')>已付款</option>
<option value="refunded" @selected($order->payment_status==='refunded')>已退款</option>
</select>
<button class="btn btn-primary w-100">儲存</button>
</form>
</div>
</div>
</div>
@endsection
