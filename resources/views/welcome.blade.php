<!doctype html>
<html lang="zh-Hant">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>布袋港－澎湖船票訂位</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:#f4f6f8;color:#263238}.hero{max-width:1100px;margin:0 auto;padding:70px 16px}.brand{color:#315d91;font-weight:700}.search-card{background:#fff;border:1px solid #dde3e8;border-radius:16px;padding:28px;box-shadow:0 8px 30px rgba(0,0,0,.05)}.title{font-weight:700;font-size:42px}.muted{color:#607080}
</style>
</head>
<body>
<div class="hero">
<div class="d-flex justify-content-between align-items-center mb-5"><div class="brand">布袋港－澎湖船票</div><a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">管理後台</a></div>
<div class="row align-items-center g-5">
<div class="col-lg-6"><div class="badge text-bg-light mb-3">ONLINE BOOKING</div><h1 class="title">簡單預訂布袋港<br>往返澎湖船票</h1><p class="muted mt-3">選擇日期與方向，查詢可訂航次，再填寫票種與旅客資料即可完成訂位。</p></div>
<div class="col-lg-6">
<div class="search-card">
<h2 class="h4 mb-4">查詢航次</h2>
<form method="GET" action="{{ route('booking.search') }}">
<div class="mb-3"><label class="form-label">搭船日期</label><input type="date" class="form-control form-control-lg" name="date" required min="{{ now()->format('Y-m-d') }}" value="{{ now()->format('Y-m-d') }}"></div>
<div class="mb-4"><label class="form-label">方向</label>
<div class="row g-2">
<div class="col-6"><input class="btn-check" type="radio" name="direction" value="outbound" id="outbound" checked><label class="btn btn-outline-primary w-100 py-3" for="outbound">布袋港 → 澎湖</label></div>
<div class="col-6"><input class="btn-check" type="radio" name="direction" value="return" id="return"><label class="btn btn-outline-primary w-100 py-3" for="return">澎湖 → 布袋港</label></div>
</div></div>
<button class="btn btn-primary btn-lg w-100" type="submit">查詢航次</button>
</form>
</div></div>
</div>
</div>
</body>
</html>
