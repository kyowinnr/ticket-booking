@extends('layouts.booking')
@section('content')
<form method="GET" action="{{ route('booking.create') }}" id="tripSelectionForm">
<div class="d-flex justify-content-between align-items-center mb-4">
<div>
<h1 class="h3 mb-2">{{ $mode === 'round_trip' ? '選擇來回航次' : '選擇航次' }}</h1>
<div class="text-muted">去程 {{ $outboundDate }} @if($mode === 'round_trip') ・ 回程 {{ $returnDate }} @endif</div>
</div>
<a href="{{ route('home') }}" class="btn btn-outline-secondary">重新搜尋</a>
</div>

<div class="card p-4 mb-4">
<h2 class="h5 mb-3">去程｜{{ $outboundDate }}　布袋港 → 澎湖</h2>
@if(!$outboundRoute)
<div class="alert alert-warning mb-0">目前尚未建立布袋港 → 澎湖航線。</div>
@elseif($outboundTrips->isEmpty())
<div class="alert alert-info mb-0">這一天沒有可訂去程航次。</div>
@else
<div class="row g-3">
@foreach($outboundTrips as $trip)
<div class="col-12">
<label class="card p-3 h-100" style="cursor:pointer">
<div class="d-flex align-items-center gap-3">
<input class="form-check-input mt-0" type="radio" name="outbound_trip_id" value="{{ $trip->id }}" required>
<div class="flex-grow-1">
<div class="fs-5 fw-bold">{{ $trip->departure_time->format('H:i') }} @if($trip->arrival_time) → {{ $trip->arrival_time->format('H:i') }} @endif</div>
<div class="text-muted">船舶：{{ $trip->ship->name }} ・ 剩餘 {{ $trip->available_seats }} 位</div>
</div>
</div>
</label>
</div>
@endforeach
</div>
@endif
</div>

@if($mode === 'round_trip')
<div class="card p-4 mb-4">
<h2 class="h5 mb-3">回程｜{{ $returnDate }}　澎湖 → 布袋港</h2>
@if(!$returnRoute)
<div class="alert alert-warning mb-0">目前尚未建立澎湖 → 布袋港航線。</div>
@elseif($returnTrips->isEmpty())
<div class="alert alert-info mb-0">這一天沒有可訂回程航次。</div>
@else
<div class="row g-3">
@foreach($returnTrips as $trip)
<div class="col-12">
<label class="card p-3 h-100" style="cursor:pointer">
<div class="d-flex align-items-center gap-3">
<input class="form-check-input mt-0" type="radio" name="return_trip_id" value="{{ $trip->id }}" required>
<div class="flex-grow-1">
<div class="fs-5 fw-bold">{{ $trip->departure_time->format('H:i') }} @if($trip->arrival_time) → {{ $trip->arrival_time->format('H:i') }} @endif</div>
<div class="text-muted">船舶：{{ $trip->ship->name }} ・ 剩餘 {{ $trip->available_seats }} 位</div>
</div>
</div>
</label>
</div>
@endforeach
</div>
@endif
</div>
@endif

@if($outboundTrips->isNotEmpty() && ($mode === 'one_way' || $returnTrips->isNotEmpty()))
<div class="text-end">
<button class="btn btn-primary btn-lg px-5" type="submit">下一步：填寫旅客資料</button>
</div>
@endif
</form>
@endsection
