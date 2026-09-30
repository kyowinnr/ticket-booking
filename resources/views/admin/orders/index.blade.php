@extends('layouts.admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
<div><h1 class="h3 mb-1">訂單管理</h1><div class="text-muted">查詢與處理旅客訂位</div></div>
</div>

<div class="card p-3 mb-4">
<form class="row g-2">
<div class="col-md-2"><input class="form-control" name="order_no" placeholder="訂單編號" value="{{ request('order_no') }}"></div>
<div class="col-md-2"><input class="form-control" name="contact_phone" placeholder="訂位人手機" value="{{ request('contact_phone') }}"></div>
<div class="col-md-2"><input class="form-control" type="date" name="date" value="{{ request('date') }}"></div>
<div class="col-md-2"><select class="form-select" name="status"><option value="">訂單狀態</option><option value="pending" @selected(request('status')==='pending')>待確認</option><option value="confirmed" @selected(request('status')==='confirmed')>已確認</option><option value="cancelled" @selected(request('status')==='cancelled')>已取消</option><option value="completed" @selected(request('status')==='completed')>已完成</option></select></div>
<div class="col-md-2"><select class="form-select" name="payment_status"><option value="">付款狀態</option><option value="unpaid" @selected(request('payment_status')==='unpaid')>未付款</option><option value="paid" @selected(request('payment_status')==='paid')>已付款</option><option value="refunded" @selected(request('payment_status')==='refunded')>已退款</option></select></div>
<div class="col-md-2 d-flex gap-2"><button class="btn btn-primary flex-fill">搜尋</button><a class="btn btn-outline-secondary" href="{{ route('admin.orders.index') }}">清除</a></div>
</form>
</div>

<div class="card p-0 overflow-hidden">
<div class="table-responsive">
<table class="table table-hover mb-0 align-middle">
<thead><tr><th>訂單編號</th><th>搭船日期</th><th>航線／時間</th><th>訂位人</th><th>金額</th><th>訂單</th><th>付款</th><th></th></tr></thead>
<tbody>
@forelse($orders as $order)
<tr>
<td><strong>{{ $order->order_no }}</strong></td>
<td>{{ $order->trip->departure_date->format('Y-m-d') }}</td>
<td>{{ $order->trip->route->departure_port }} → {{ $order->trip->route->arrival_port }}<br><span class="text-muted">{{ $order->trip->departure_time->format('H:i') }}</span></td>
<td>{{ $order->contact_name }}<br><span class="text-muted">{{ $order->contact_phone }}</span></td>
<td>NT$ {{ number_format($order->total_amount) }}</td>
<td>@if($order->status==='pending')<span class="badge text-bg-warning">待確認</span>@elseif($order->status==='confirmed')<span class="badge text-bg-success">已確認</span>@elseif($order->status==='cancelled')<span class="badge text-bg-secondary">已取消</span>@else<span class="badge text-bg-primary">已完成</span>@endif</td>
<td>@if($order->payment_status==='unpaid')<span class="badge text-bg-warning">未付款</span>@elseif($order->payment_status==='paid')<span class="badge text-bg-success">已付款</span>@else<span class="badge text-bg-secondary">已退款</span>@endif</td>
<td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.orders.show',$order) }}">查看</a></td>
</tr>
@empty
<tr><td colspan="8" class="text-center py-5 text-muted">查無訂單</td></tr>
@endforelse
</tbody>
</table>
</div>
</div>
<div class="mt-3">{{ $orders->links() }}</div>
@endsection
