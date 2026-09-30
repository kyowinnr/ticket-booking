<!doctype html>
<html lang="zh-Hant">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $title ?? '船票訂位管理系統' }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:#f4f6f8;color:#263238}.sidebar{min-height:100vh;background:#fff;border-right:1px solid #dde3e8}.brand{font-weight:700;font-size:20px;padding:22px 18px;border-bottom:1px solid #eee}.nav-link{color:#52616b;padding:10px 18px}.nav-link:hover,.nav-link.active{background:#eaf2ff;color:#3267a8}.content{padding:28px}.card{border:1px solid #dde3e8;box-shadow:0 2px 10px rgba(0,0,0,.03)}.table th{background:#f7f9fb;white-space:nowrap}
</style>
</head>
<body>
<div class="container-fluid">
<div class="row">
<aside class="col-md-2 col-lg-2 p-0 sidebar">
<div class="brand">船票訂位系統</div>
<nav class="py-3">
<a class="nav-link" href="{{ route('admin.dashboard') }}">📊 系統首頁</a>
<a class="nav-link" href="{{ route('admin.routes.index') }}">↔ 航線管理</a>
<a class="nav-link" href="{{ route('admin.ships.index') }}">🚢 船舶管理</a>
<a class="nav-link" href="{{ route('admin.ticket-types.index') }}">🎫 票種／票價</a>
<a class="nav-link" href="{{ route('admin.trips.index') }}">🗓 航次管理</a>
<a class="nav-link" href="{{ route('admin.orders.index') }}">📋 訂單管理</a>
</nav>
</aside>
<main class="col-md-10 col-lg-10 content">
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')
</main>
</div>
</div>
</body>
</html>
