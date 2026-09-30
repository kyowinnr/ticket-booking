<!doctype html>
<html lang="zh-Hant">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $title ?? '布袋港－澎湖船票訂位' }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:#f4f6f8;color:#263238}.navbar{background:#fff;border-bottom:1px solid #dde3e8}.brand{font-weight:700;color:#315d91}.page{max-width:1100px;margin:0 auto;padding:32px 16px}.card{border:1px solid #dde3e8;box-shadow:0 4px 18px rgba(0,0,0,.04)}.section-title{font-weight:700;margin-bottom:18px}.price{font-weight:700;color:#315d91}
</style>
</head>
<body>
<nav class="navbar">
<div class="container"><a class="navbar-brand brand" href="{{ route('home') }}">布袋港－澎湖船票</a><a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary">管理後台</a></div>
</nav>
<main class="page">
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')
</main>
</body>
</html>
