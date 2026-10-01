@extends('layouts.booking')

@section('content')
<form method="POST" action="{{ route('booking.store') }}" id="bookingForm">
@csrf

<input type="hidden" name="outbound_trip_id" value="{{ $outboundTrip->id }}">
@if($returnTrip)
<input type="hidden" name="return_trip_id" value="{{ $returnTrip->id }}">
@endif

<h1 class="h3 mb-4">填寫訂位資料</h1>

<div class="row g-4 mb-4">
    <div class="col-lg-{{ $returnTrip ? '6' : '12' }}">
        <div class="card p-4 h-100">
            <div class="section-title">去程</div>
            <div class="fw-bold">
                {{ $outboundTrip->departure_date->format('Y-m-d') }}
                {{ $outboundTrip->route->departure_port }} →
                {{ $outboundTrip->route->arrival_port }}
            </div>
            <div class="text-muted mt-1">
                {{ $outboundTrip->departure_time->format('H:i') }}
                @if($outboundTrip->arrival_time)
                    → {{ $outboundTrip->arrival_time->format('H:i') }}
                @endif
                ・ {{ $outboundTrip->ship->name }}
                ・ 剩餘 {{ $outboundTrip->available_seats }} 位
            </div>
        </div>
    </div>

    @if($returnTrip)
    <div class="col-lg-6">
        <div class="card p-4 h-100">
            <div class="section-title">回程</div>
            <div class="fw-bold">
                {{ $returnTrip->departure_date->format('Y-m-d') }}
                {{ $returnTrip->route->departure_port }} →
                {{ $returnTrip->route->arrival_port }}
            </div>
            <div class="text-muted mt-1">
                {{ $returnTrip->departure_time->format('H:i') }}
                @if($returnTrip->arrival_time)
                    → {{ $returnTrip->arrival_time->format('H:i') }}
                @endif
                ・ {{ $returnTrip->ship->name }}
                ・ 剩餘 {{ $returnTrip->available_seats }} 位
            </div>
        </div>
    </div>
    @endif
</div>

<div class="card p-4 mb-4">
    <div class="section-title">去程票種與旅客</div>
    <div id="outboundTickets"></div>
    <div class="mt-3 fw-bold">
        去程合計：<span id="outboundCount">0</span> 位　
        NT$ <span id="outboundAmount">0</span>
    </div>
</div>

@if($returnTrip)
<div class="card p-4 mb-4">
    <div class="section-title">回程票種與旅客</div>
    <div id="returnTickets"></div>
    <div class="mt-3 fw-bold">
        回程合計：<span id="returnCount">0</span> 位　
        NT$ <span id="returnAmount">0</span>
    </div>
</div>
@endif

<div class="card p-4 mb-4">
    <div class="section-title">訂位人</div>

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">姓名 *</label>
            <input
                name="contact_name"
                class="form-control"
                required
                value="{{ old('contact_name') }}"
            >
        </div>

        <div class="col-md-4">
            <label class="form-label">手機 *</label>
            <input
                name="contact_phone"
                class="form-control"
                required
                value="{{ old('contact_phone') }}"
            >
        </div>

        <div class="col-md-4">
            <label class="form-label">Email</label>
            <input
                name="contact_email"
                type="email"
                class="form-control"
                value="{{ old('contact_email') }}"
            >
        </div>

        <div class="col-12">
            <label class="form-label">備註</label>
            <textarea
                name="note"
                class="form-control"
                rows="2"
            >{{ old('note') }}</textarea>
        </div>
    </div>
</div>

<div class="card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title mb-0">訂位摘要</div>
        <div class="fs-5 fw-bold">
            總金額 NT$ <span id="grandTotal">0</span>
        </div>
    </div>
</div>

<div class="text-end">
    <button class="btn btn-primary btn-lg px-5" type="submit">
        確認訂位
    </button>
</div>

</form>

<script>
const ticketMeta = @json(
    $ticketTypes->map(function ($ticket) {
        return [
            'id' => $ticket->id,
            'name' => $ticket->name,
            'price' => (float) $ticket->price,
            'description' => $ticket->description,
        ];
    })->values()
);

const hasReturn = {{ $returnTrip ? 'true' : 'false' }};

function renderLeg(leg) {
    const box = document.getElementById(leg + 'Tickets');

    if (!box) {
        return;
    }

    let html = '';

    ticketMeta.forEach(function (meta) {
        html += `
            <div class="row align-items-center border-bottom py-3">
                <div class="col">
                    <div class="fw-semibold">${meta.name}</div>
                    ${meta.description
                        ? '<div class="text-muted small">' + meta.description + '</div>'
                        : ''}
                </div>

                <div class="col-auto price">
                    NT$ ${Number(meta.price).toLocaleString()}
                </div>

                <div class="col-3 col-md-2">
                    <label class="form-label small mb-1">張數</label>
                    <input
                        class="form-control qty-${leg}"
                        type="number"
                        min="0"
                        max="20"
                        value="0"
                        data-id="${meta.id}"
                        data-price="${meta.price}"
                        name="legs[${leg}][ticket_quantities][${meta.id}]"
                    >
                </div>
            </div>
        `;
    });

    box.innerHTML = html;

    box.querySelectorAll('.qty-' + leg).forEach(function (input) {
        input.addEventListener('input', function () {
            renderPassengers(leg);
        });

        input.addEventListener('change', function () {
            renderPassengers(leg);
        });
    });

    renderPassengers(leg);
}

function renderPassengers(leg) {
    const box = document.getElementById(leg + 'Tickets');

    if (!box) {
        return;
    }

    let passengerHtml = '';
    let total = 0;
    let amount = 0;
    let index = 0;

    box.querySelectorAll('.qty-' + leg).forEach(function (input) {
        const quantity = parseInt(input.value || 0, 10);
        const ticketTypeId = parseInt(input.dataset.id, 10);
        const price = parseFloat(input.dataset.price);

        const meta = ticketMeta.find(function (item) {
            return Number(item.id) === ticketTypeId;
        });

        if (quantity <= 0 || !meta) {
            return;
        }

        total += quantity;
        amount += quantity * price;

        for (let i = 1; i <= quantity; i++) {
            const passengerNumber = total - quantity + i;

            passengerHtml += `
                <div class="border rounded p-3 mt-3">
                    <div class="fw-semibold mb-2">
                        旅客 ${passengerNumber} ・ ${meta.name}
                    </div>

                    <input
                        type="hidden"
                        name="legs[${leg}][passengers][${index}][ticket_type_id]"
                        value="${ticketTypeId}"
                    >

                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">姓名 *</label>
                            <input
                                required
                                class="form-control"
                                name="legs[${leg}][passengers][${index}][name]"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">身分證／證件號碼</label>
                            <input
                                class="form-control"
                                name="legs[${leg}][passengers][${index}][id_number]"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">生日</label>
                            <input
                                type="date"
                                class="form-control"
                                name="legs[${leg}][passengers][${index}][birthday]"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">手機</label>
                            <input
                                class="form-control"
                                name="legs[${leg}][passengers][${index}][phone]"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">性別</label>
                            <select
                                class="form-select"
                                name="legs[${leg}][passengers][${index}][gender]"
                            >
                                <option value="">請選擇</option>
                                <option value="男">男</option>
                                <option value="女">女</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;

            index++;
        }
    });

    const existing = box.querySelector('.passenger-list');

    if (existing) {
        existing.remove();
    }

    const wrapper = document.createElement('div');
    wrapper.className = 'passenger-list';

    wrapper.innerHTML = passengerHtml
        || '<div class="text-muted mt-3">請先選擇票數。</div>';

    box.appendChild(wrapper);

    const countElement = document.getElementById(leg + 'Count');
    const amountElement = document.getElementById(leg + 'Amount');

    if (countElement) {
        countElement.textContent = total;
    }

    if (amountElement) {
        amountElement.textContent = Math.round(amount).toLocaleString();
    }

    updateGrandTotal();
}

function updateGrandTotal() {
    let total = 0;

    ['outbound', 'return'].forEach(function (leg) {
        const element = document.getElementById(leg + 'Amount');

        if (element) {
            total += parseInt(
                element.textContent.replace(/,/g, '') || '0',
                10
            );
        }
    });

    const grandTotal = document.getElementById('grandTotal');

    if (grandTotal) {
        grandTotal.textContent = total.toLocaleString();
    }
}

renderLeg('outbound');

if (hasReturn) {
    renderLeg('return');
}
</script>
@endsection
