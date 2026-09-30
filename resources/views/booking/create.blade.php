@extends('layouts.booking')
@section('content')
<form method="POST" action="{{ route('booking.store',$trip) }}" id="bookingForm">
@csrf
<h1 class="h3 mb-4">填寫訂位資料</h1>

<div class="card p-4 mb-4">
<div class="section-title">航次資訊</div>
<div>{{ $trip->departure_date->format('Y-m-d') }}　{{ $trip->route->departure_port }} → {{ $trip->route->arrival_port }}</div>
<div class="text-muted mt-1">{{ $trip->departure_time->format('H:i') }} ・ {{ $trip->ship->name }} ・ 剩餘 {{ $trip->available_seats }} 位</div>
</div>

<div class="card p-4 mb-4">
<div class="section-title">購票數量</div>
@foreach($ticketTypes as $type)
<div class="row align-items-center border-bottom py-3">
<div class="col"><div class="fw-semibold">{{ $type->name }}</div>@if($type->description)<div class="text-muted small">{{ $type->description }}</div>@endif</div>
<div class="col-auto price">NT$ {{ number_format($type->price) }}</div>
<div class="col-3 col-md-2"><input class="form-control qty" type="number" min="0" max="20" value="0" name="ticket_quantities[{{ $type->id }}]" data-type="{{ $type->id }}" data-name="{{ $type->name }}" onchange="renderPassengers()"></div>
</div>
@endforeach
<div class="mt-3 fw-bold">合計人數：<span id="totalCount">0</span>　合計金額：NT$ <span id="totalAmount">0</span></div>
</div>

<div class="card p-4 mb-4">
<div class="section-title">訂位人</div>
<div class="row g-3">
<div class="col-md-4"><label class="form-label">姓名 *</label><input name="contact_name" class="form-control" required value="{{ old('contact_name') }}"></div>
<div class="col-md-4"><label class="form-label">手機 *</label><input name="contact_phone" class="form-control" required value="{{ old('contact_phone') }}"></div>
<div class="col-md-4"><label class="form-label">Email</label><input name="contact_email" type="email" class="form-control" value="{{ old('contact_email') }}"></div>
<div class="col-12"><label class="form-label">備註</label><textarea name="note" class="form-control" rows="2">{{ old('note') }}</textarea></div>
</div>
</div>

<div class="card p-4 mb-4">
<div class="section-title">旅客資料</div>
<div id="passengers"><div class="text-muted">請先選擇票數。</div></div>
</div>

<div class="text-end"><button class="btn btn-primary btn-lg" type="submit">確認訂位</button></div>
</form>

<script>
const ticketMeta = @json($ticketTypes->map(fn($t)=>['id'=>$t->id,'name'=>$t->name,'price'=>(float)$t->price])->values());
function renderPassengers(){
 const box=document.getElementById('passengers');
 const qtyInputs=document.querySelectorAll('.qty');
 let total=0, html='', index=0, amount=0;
 qtyInputs.forEach(input=>{
   const q=parseInt(input.value||0); const id=parseInt(input.dataset.type);
   const meta=ticketMeta.find(x=>x.id===id);
   if(q>0 && meta){ for(let i=1;i<=q;i++){ total++; amount+=meta.price;
      html+=`<div class="border rounded p-3 mb-3"><div class="fw-semibold mb-2">旅客 ${total} ・ ${meta.name}</div>
      <input type="hidden" name="passengers[${index}][ticket_type_id]" value="${id}">
      <div class="row g-2">
      <div class="col-md-4"><label class="form-label">姓名 *</label><input required class="form-control" name="passengers[${index}][name]"></div>
      <div class="col-md-4"><label class="form-label">身分證／證件號碼</label><input class="form-control" name="passengers[${index}][id_number]"></div>
      <div class="col-md-4"><label class="form-label">生日</label><input type="date" class="form-control" name="passengers[${index}][birthday]"></div>
      <div class="col-md-4"><label class="form-label">手機</label><input class="form-control" name="passengers[${index}][phone]"></div>
      <div class="col-md-4"><label class="form-label">性別</label><select class="form-select" name="passengers[${index}][gender]"><option value="">請選擇</option><option value="男">男</option><option value="女">女</option></select></div>
      </div></div>`;
      index++;
   }}
 });
 box.innerHTML=html||'<div class="text-muted">請先選擇票數。</div>';
 document.getElementById('totalCount').textContent=total;
 document.getElementById('totalAmount').textContent=Math.round(amount).toLocaleString();
}
renderPassengers();
</script>
@endsection
