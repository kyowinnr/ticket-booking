@extends('layouts.admin')
@section('content')
<h3 class="mb-3">票種／票價管理</h3>
<div class="card p-3 mb-4"><form method="post" action="{{ route('admin.ticket-types.store') }}" class="row g-2">@csrf
<div class="col-md-3"><input name="name" class="form-control" placeholder="例如：成人票" required></div>
<div class="col-md-2"><input name="price" type="number" min="0" step="1" class="form-control" placeholder="票價" required></div>
<div class="col-md-4"><input name="description" class="form-control" placeholder="備註"></div>
<div class="col-md-2 form-check align-self-center"><input type="checkbox" name="status" value="1" class="form-check-input" checked> 啟用</div>
<div class="col-md-1"><button class="btn btn-primary w-100">新增</button></div></form></div>
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>票種</th><th>票價</th><th>狀態</th><th>備註</th><th>操作</th></tr></thead><tbody>
@forelse($ticketTypes as $ticket)<tr><td>{{ $ticket->name }}</td><td>{{ number_format($ticket->price) }}</td><td>{{ $ticket->status?'啟用':'停用' }}</td><td>{{ $ticket->description }}</td><td><form method="post" action="{{ route('admin.ticket-types.destroy',$ticket) }}" onsubmit="return confirm('確定刪除？')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">刪除</button></form></td></tr>@empty<tr><td colspan="5" class="text-center py-4">尚無票種</td></tr>@endforelse
</tbody></table></div><div class="p-3">{{ $ticketTypes->links() }}</div></div>
@endsection
