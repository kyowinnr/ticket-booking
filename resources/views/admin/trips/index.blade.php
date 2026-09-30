@extends('layouts.admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h3>航次管理</h3></div>
<div class="card p-3 mb-4"><form method="post" action="{{ route('admin.trips.store') }}" class="row g-2">@csrf
<div class="col-md-2"><select name="route_id" class="form-select" required><option value="">選擇航線</option>@foreach($routes as $route)<option value="{{ $route->id }}">{{ $route->name }}</option>@endforeach</select></div>
<div class="col-md-2"><select name="ship_id" class="form-select" required><option value="">選擇船舶</option>@foreach($ships as $ship)<option value="{{ $ship->id }}">{{ $ship->name }}（{{ $ship->capacity }}）</option>@endforeach</select></div>
<div class="col-md-2"><input type="date" name="departure_date" class="form-control" required></div>
<div class="col-md-1"><input type="time" name="departure_time" class="form-control" required></div>
<div class="col-md-1"><input type="time" name="arrival_time" class="form-control"></div>
<div class="col-md-1"><input type="number" name="capacity" min="1" class="form-control" placeholder="座位" required></div>
<div class="col-md-2"><input name="note" class="form-control" placeholder="備註"></div>
<div class="col-md-1"><button class="btn btn-primary w-100">新增</button></div>
</form></div>
<div class="card p-3 mb-3"><form class="row g-2 align-items-center"><div class="col-auto"><label>查詢日期</label></div><div class="col-auto"><input type="date" name="date" value="{{ request('date') }}" class="form-control"></div><div class="col-auto"><button class="btn btn-outline-primary">查詢</button></div><div class="col-auto"><a href="{{ route('admin.trips.index') }}" class="btn btn-outline-secondary">全部</a></div></form></div>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>日期</th><th>時間</th><th>航線</th><th>船舶</th><th>可售</th><th>已訂</th><th>剩餘</th><th>狀態</th><th>操作</th></tr></thead><tbody>
@forelse($trips as $trip)<tr><td>{{ $trip->departure_date->format('Y-m-d') }}</td><td>{{ substr($trip->departure_time,0,5) }}</td><td>{{ $trip->route->name }}</td><td>{{ $trip->ship->name }}</td><td>{{ $trip->capacity }}</td><td>{{ $trip->booked_count }}</td><td><strong>{{ $trip->available_seats }}</strong></td><td>{{ ['open'=>'開放','closed'=>'關閉','cancelled'=>'取消'][$trip->status] }}</td><td>
<form method="post" action="{{ route('admin.trips.destroy',$trip) }}" onsubmit="return confirm('確定刪除此航次？')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">刪除</button></form>
</td></tr>@empty<tr><td colspan="9" class="text-center py-4">尚無航次</td></tr>@endforelse
</tbody></table></div><div class="p-3">{{ $trips->links() }}</div></div>
@endsection