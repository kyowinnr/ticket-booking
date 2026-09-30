@extends('layouts.admin')
@section('content')
<h3 class="mb-4">系統首頁</h3>
<div class="row g-3">
@foreach([['航線',$routeCount,'routes.index'],['船舶',$shipCount,'ships.index'],['票種',$ticketTypeCount,'ticket-types.index'],['未來航次',$tripCount,null]] as $item)
<div class="col-md-3"><div class="card p-4"><div class="text-secondary">{{ $item[0] }}</div><div class="display-6 fw-bold mt-2">{{ $item[1] }}</div>@if($item[2])<a href="{{ route('admin.'.$item[2]) }}" class="small mt-2">管理資料 →</a>@endif</div></div>
@endforeach
</div>
<div class="card mt-4 p-4">
<h5>目前系統</h5>
<p class="text-secondary mb-0">布袋港 ↔ 澎湖船票訂位系統。金流與實體座位選位目前不納入第一階段。</p>
</div>
@endsection
