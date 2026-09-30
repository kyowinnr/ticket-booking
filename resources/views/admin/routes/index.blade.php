@extends('layouts.admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h3>航線管理</h3></div>
<div class="card p-3 mb-4"><form method="post" action="{{ route('admin.routes.store') }}" class="row g-2">@csrf
<div class="col-md-3"><input name="name" class="form-control" placeholder="航線名稱" required></div>
<div class="col-md-3"><input name="departure_port" class="form-control" placeholder="出發港" required></div>
<div class="col-md-3"><input name="arrival_port" class="form-control" placeholder="抵達港" required></div>
<div class="col-md-2 form-check align-self-center"><input type="checkbox" name="status" value="1" class="form-check-input" checked> 啟用</div>
<div class="col-md-1"><button class="btn btn-primary w-100">新增</button></div>
</form></div>
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>名稱</th><th>出發</th><th>抵達</th><th>狀態</th><th>操作</th></tr></thead><tbody>
@forelse($routes as $route)<tr><td>{{ $route->name }}</td><td>{{ $route->departure_port }}</td><td>{{ $route->arrival_port }}</td><td>{{ $route->status?'啟用':'停用' }}</td><td><form method="post" action="{{ route('admin.routes.destroy',$route) }}" onsubmit="return confirm('確定刪除？')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">刪除</button></form></td></tr>@empty<tr><td colspan="5" class="text-center py-4">尚無航線</td></tr>@endforelse
</tbody></table></div><div class="p-3">{{ $routes->links() }}</div></div>
@endsection
