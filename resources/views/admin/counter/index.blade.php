@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">櫃台快速操作</h2>
        <div class="text-muted">輸入訂單編號，快速確認訂單與收款。</div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.counter.index') }}" class="row g-2">
            <div class="col-md-8">
                <input type="text" name="order_no" value="{{ request('order_no') }}"
                       class="form-control form-control-lg" placeholder="請輸入訂單編號，例如 BT260930123456ABCD">
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary btn-lg w-100">查詢訂單</button>
            </div>
        </form>
    </div>
</div>

@if(request('order_no') && !$order)
<div class="alert alert-warning">找不到這筆訂單，請確認訂單編號。</div>
@endif

@if($order)
<div class="card">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <strong>訂單 {{ $order->order_no }}</strong>
            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">查看完整訂單</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col-md-3"><div class="text-muted small">搭船日期</div><strong>{{ $order->trip->departure_date->format('Y-m-d') }}</strong></div>
            <div class="col-md-3"><div class="text-muted small">時間</div><strong>{{ substr($order->trip->departure_time, 0, 5) }}</strong></div>
            <div class="col-md-3"><div class="text-muted small">訂位人</div><strong>{{ $order->contact_name }}</strong></div>
            <div class="col-md-3"><div class="text-muted small">電話</div><strong>{{ $order->contact_phone }}</strong></div>
        </div>

        <div class="p-3 bg-light rounded mb-4">
            <div class="fw-bold mb-2">{{ $order->trip->route->departure_port }} → {{ $order->trip->route->arrival_port }}</div>
            <div class="text-muted">{{ $order->trip->ship->name }}</div>
        </div>

        <div class="table-responsive mb-4">
            <table class="table align-middle">
                <thead><tr><th>旅客</th><th>票種</th><th>身分證字號</th></tr></thead>
                <tbody>
                @foreach($order->passengers as $passenger)
                    <tr>
                        <td>{{ $passenger->name }}</td>
                        <td>{{ $passenger->ticketType->name }}</td>
                        <td>{{ $passenger->id_number ?: '—' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="row align-items-center g-3">
            <div class="col-md-4">
                <div class="text-muted">應收金額</div>
                <div class="fs-3 fw-bold text-primary">NT$ {{ number_format($order->total_amount) }}</div>
            </div>
            <div class="col-md-4">
                <div>訂單狀態：
                    <span class="badge text-bg-{{ $order->status === 'cancelled' ? 'danger' : ($order->status === 'confirmed' ? 'success' : 'warning') }}">
                        {{ $order->status }}
                    </span>
                </div>
                <div class="mt-2">付款狀態：
                    <span class="badge text-bg-{{ $order->payment_status === 'paid' ? 'success' : 'secondary' }}">
                        {{ $order->payment_status }}
                    </span>
                </div>
            </div>
            <div class="col-md-4">
                @if($order->status !== 'cancelled')
                    <div class="d-grid gap-2">
                        @if($order->status !== 'confirmed')
                            <form method="POST" action="{{ route('admin.counter.confirm', $order) }}">
                                @csrf @method('PUT')
                                <button class="btn btn-outline-primary w-100">確認訂單</button>
                            </form>
                        @endif

                        @if($order->payment_status !== 'paid')
                            <form method="POST" action="{{ route('admin.counter.paid', $order) }}">
                                @csrf @method('PUT')
                                <button class="btn btn-outline-success w-100">確認收款</button>
                            </form>
                        @endif

                        @if($order->status !== 'confirmed' || $order->payment_status !== 'paid')
                            <form method="POST" action="{{ route('admin.counter.confirm-paid', $order) }}">
                                @csrf @method('PUT')
                                <button class="btn btn-primary w-100">確認訂單＋已收款</button>
                            </form>
                        @endif
                    </div>
                @else
                    <div class="alert alert-danger mb-0">此訂單已取消，不能進行櫃台確認或收款。</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@endsection
