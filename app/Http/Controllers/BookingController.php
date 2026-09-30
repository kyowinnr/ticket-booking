<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPassenger;
use App\Models\Route as ShippingRoute;
use App\Models\TicketType;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function search(Request $request)
    {
        $request->merge([
            'mode' => $request->input('mode', 'one_way'),
            'outbound_date' => $request->input('outbound_date', now()->toDateString()),
            'return_date' => $request->input('return_date', now()->toDateString()),
        ]);

        $data = $request->validate([
            'mode' => ['required', 'in:one_way,round_trip'],
            'outbound_date' => ['required', 'date', 'after_or_equal:today'],
            'return_date' => ['required_if:mode,round_trip', 'date', 'after_or_equal:outbound_date'],
        ]);

        $outboundRoute = $this->findRoute('outbound');
        $returnRoute = $this->findRoute('return');

        $outboundTrips = $outboundRoute
            ? Trip::with(['route', 'ship'])
                ->where('route_id', $outboundRoute->id)
                ->whereDate('departure_date', $data['outbound_date'])
                ->where('status', 'open')
                ->orderBy('departure_time')
                ->get()
            : collect();

        $returnTrips = $data['mode'] === 'round_trip' && $returnRoute
            ? Trip::with(['route', 'ship'])
                ->where('route_id', $returnRoute->id)
                ->whereDate('departure_date', $data['return_date'])
                ->where('status', 'open')
                ->orderBy('departure_time')
                ->get()
            : collect();

        return view('booking.trips', [
            'mode' => $data['mode'],
            'outboundDate' => $data['outbound_date'],
            'returnDate' => $data['return_date'],
            'outboundRoute' => $outboundRoute,
            'returnRoute' => $returnRoute,
            'outboundTrips' => $outboundTrips,
            'returnTrips' => $returnTrips,
        ]);
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'outbound_trip_id' => ['required', 'integer', 'exists:trips,id'],
            'return_trip_id' => ['nullable', 'integer', 'different:outbound_trip_id', 'exists:trips,id'],
        ]);

        $trips = collect([
            'outbound' => Trip::with(['route', 'ship'])->findOrFail($data['outbound_trip_id']),
            'return' => !empty($data['return_trip_id'])
                ? Trip::with(['route', 'ship'])->findOrFail($data['return_trip_id'])
                : null,
        ]);

        foreach ($trips as $trip) {
            if (!$trip) {
                continue;
            }

            abort_unless(
                $trip->status === 'open' &&
                ($trip->departure_date->isToday() || $trip->departure_date->isFuture()) &&
                $trip->available_seats > 0,
                404
            );
        }

        $ticketTypes = TicketType::where('status', true)
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        return view('booking.create', [
            'outboundTrip' => $trips['outbound'],
            'returnTrip' => $trips['return'],
            'ticketTypes' => $ticketTypes,
        ]);
    }

    public function store(Request $request)
    {
        $ticketTypes = TicketType::where('status', true)->get()->keyBy('id');

        $data = $request->validate([
            'outbound_trip_id' => ['required', 'integer', 'exists:trips,id'],
            'return_trip_id' => ['nullable', 'integer', 'different:outbound_trip_id', 'exists:trips,id'],
            'contact_name' => ['required', 'string', 'max:100'],
            'contact_phone' => ['required', 'string', 'max:30'],
            'contact_email' => ['nullable', 'email', 'max:150'],
            'note' => ['nullable', 'string', 'max:1000'],
            'legs' => ['required', 'array'],
            'legs.outbound.ticket_quantities' => ['required', 'array'],
            'legs.outbound.ticket_quantities.*' => ['nullable', 'integer', 'min:0', 'max:20'],
            'legs.outbound.passengers' => ['required', 'array'],
            'legs.outbound.passengers.*.name' => ['required', 'string', 'max:100'],
            'legs.outbound.passengers.*.ticket_type_id' => ['required', 'integer'],
            'legs.outbound.passengers.*.id_number' => ['nullable', 'string', 'max:30'],
            'legs.outbound.passengers.*.birthday' => ['nullable', 'date'],
            'legs.outbound.passengers.*.phone' => ['nullable', 'string', 'max:30'],
            'legs.outbound.passengers.*.gender' => ['nullable', 'string', 'max:20'],
            'legs.return.ticket_quantities' => ['nullable', 'array'],
            'legs.return.ticket_quantities.*' => ['nullable', 'integer', 'min:0', 'max:20'],
            'legs.return.passengers' => ['nullable', 'array'],
            'legs.return.passengers.*.name' => ['required', 'string', 'max:100'],
            'legs.return.passengers.*.ticket_type_id' => ['required', 'integer'],
            'legs.return.passengers.*.id_number' => ['nullable', 'string', 'max:30'],
            'legs.return.passengers.*.birthday' => ['nullable', 'date'],
            'legs.return.passengers.*.phone' => ['nullable', 'string', 'max:30'],
            'legs.return.passengers.*.gender' => ['nullable', 'string', 'max:20'],
        ]);

        $tripIds = [
            'outbound' => (int) $data['outbound_trip_id'],
        ];

        if (!empty($data['return_trip_id'])) {
            $tripIds['return'] = (int) $data['return_trip_id'];
        }

        $preparedLegs = [];
        $totalAmount = 0;

        foreach ($tripIds as $leg => $tripId) {
            $legData = $data['legs'][$leg] ?? [];
            $quantities = [];
            $totalPassengers = 0;
            $legAmount = 0;

            foreach (($legData['ticket_quantities'] ?? []) as $ticketTypeId => $quantity) {
                $quantity = (int) $quantity;
                if ($quantity <= 0) {
                    continue;
                }

                $ticketTypeId = (int) $ticketTypeId;
                if (!$ticketTypes->has($ticketTypeId)) {
                    return back()->withInput()->with('error', '票種資料無效。');
                }

                $price = (float) $ticketTypes->get($ticketTypeId)->price;
                $quantities[$ticketTypeId] = $quantity;
                $totalPassengers += $quantity;
                $legAmount += $quantity * $price;
            }

            if ($totalPassengers < 1) {
                return back()->withInput()->with('error', ($leg === 'outbound' ? '去程' : '回程') . '至少要選擇 1 張票。');
            }

            $passengers = $legData['passengers'] ?? [];
            if (count($passengers) !== $totalPassengers) {
                return back()->withInput()->with('error', ($leg === 'outbound' ? '去程' : '回程') . '旅客人數必須與購票數量相同。');
            }

            $passengerTypeCounts = [];
            foreach ($passengers as $passenger) {
                $typeId = (int) $passenger['ticket_type_id'];
                if (!$ticketTypes->has($typeId)) {
                    return back()->withInput()->with('error', '旅客資料的票種無效。');
                }
                $passengerTypeCounts[$typeId] = ($passengerTypeCounts[$typeId] ?? 0) + 1;
            }

            foreach ($quantities as $typeId => $quantity) {
                if (($passengerTypeCounts[$typeId] ?? 0) !== $quantity) {
                    return back()->withInput()->with('error', ($leg === 'outbound' ? '去程' : '回程') . '旅客資料的票種數量不一致。');
                }
            }

            $preparedLegs[$leg] = [
                'trip_id' => $tripId,
                'quantities' => $quantities,
                'passengers' => $passengers,
                'totalPassengers' => $totalPassengers,
                'amount' => $legAmount,
            ];

            $totalAmount += $legAmount;
        }

        $order = DB::transaction(function () use ($data, $preparedLegs, $ticketTypes, $totalAmount) {
            $lockedTrips = [];

            foreach ($preparedLegs as $leg => $prepared) {
                $lockedTrip = Trip::whereKey($prepared['trip_id'])->lockForUpdate()->firstOrFail();

                if (
                    $lockedTrip->status !== 'open' ||
                    (!$lockedTrip->departure_date->isToday() && !$lockedTrip->departure_date->isFuture())
                ) {
                    abort(409, '所選航次目前無法訂位。');
                }

                if ($prepared['totalPassengers'] > $lockedTrip->capacity - $lockedTrip->booked_count) {
                    abort(409, ($leg === 'outbound' ? '去程' : '回程') . '剩餘座位不足。');
                }

                $lockedTrips[$leg] = $lockedTrip;
            }

            do {
                $orderNo = 'BT' . now()->format('ymdHis') . Str::upper(Str::random(4));
            } while (Order::where('order_no', $orderNo)->exists());

            $primaryTrip = $lockedTrips['outbound'];

            $order = Order::create([
                'order_no' => $orderNo,
                'trip_id' => $primaryTrip->id,
                'contact_name' => $data['contact_name'],
                'contact_phone' => $data['contact_phone'],
                'contact_email' => $data['contact_email'] ?? null,
                'total_amount' => $totalAmount,
                'payment_status' => 'unpaid',
                'status' => 'pending',
                'note' => $data['note'] ?? null,
            ]);

            foreach ($preparedLegs as $leg => $prepared) {
                $lockedTrip = $lockedTrips[$leg];

                foreach ($prepared['quantities'] as $typeId => $quantity) {
                    $price = (float) $ticketTypes->get($typeId)->price;

                    $item = OrderItem::create([
                        'order_id' => $order->id,
                        'trip_id' => $lockedTrip->id,
                        'ticket_type_id' => $typeId,
                        'quantity' => $quantity,
                        'unit_price' => $price,
                        'subtotal' => $price * $quantity,
                    ]);

                    $remaining = $quantity;

                    foreach ($prepared['passengers'] as $passenger) {
                        if ((int) $passenger['ticket_type_id'] !== (int) $typeId || $remaining <= 0) {
                            continue;
                        }

                        OrderPassenger::create([
                            'order_id' => $order->id,
                            'order_item_id' => $item->id,
                            'ticket_type_id' => $typeId,
                            'name' => $passenger['name'],
                            'id_number' => $passenger['id_number'] ?? null,
                            'birthday' => $passenger['birthday'] ?? null,
                            'phone' => $passenger['phone'] ?? null,
                            'gender' => $passenger['gender'] ?? null,
                        ]);

                        $remaining--;
                    }
                }

                $lockedTrip->increment('booked_count', $prepared['totalPassengers']);
            }

            return $order;
        });

        return redirect()->route('booking.success', $order);
    }

    public function success(Order $order)
    {
        $order->load([
            'trip.route',
            'trip.ship',
            'items.trip.route',
            'items.trip.ship',
            'items.ticketType',
            'passengers.ticketType',
            'passengers.orderItem',
        ]);

        return view('booking.success', compact('order'));
    }

    private function findRoute(string $direction): ?ShippingRoute
    {
        return ShippingRoute::where('status', true)->get()->first(function ($item) use ($direction) {
            if ($direction === 'outbound') {
                return str_contains($item->departure_port, '布袋') && str_contains($item->arrival_port, '澎湖');
            }

            return str_contains($item->departure_port, '澎湖') && str_contains($item->arrival_port, '布袋');
        });
    }
}
