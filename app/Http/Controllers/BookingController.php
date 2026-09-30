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
            'date' => $request->input('date', now()->toDateString()),
            'direction' => $request->input('direction', 'outbound'),
        ]);

        $data = $request->validate([
            'date' => ['required','date','after_or_equal:today'],
            'direction' => ['required','in:outbound,return'],
        ]);

        $routes = ShippingRoute::where('status', true)->get();
        $route = $routes->first(function ($item) use ($data) {
            if ($data['direction'] === 'outbound') {
                return str_contains($item->departure_port, '布袋') && str_contains($item->arrival_port, '澎湖');
            }

            return str_contains($item->departure_port, '澎湖') && str_contains($item->arrival_port, '布袋');
        });

        $trips = $route
            ? Trip::with(['route', 'ship'])
                ->where('route_id', $route->id)
                ->whereDate('departure_date', $data['date'])
                ->where('status', 'open')
                ->orderBy('departure_time')
                ->get()
            : collect();

        return view('booking.trips', [
            'date' => $data['date'],
            'direction' => $data['direction'],
            'route' => $route,
            'trips' => $trips,
        ]);
    }

    public function create(Trip $trip)
    {
        abort_unless(
            $trip->status === 'open' &&
            ($trip->departure_date->isToday() || $trip->departure_date->isFuture()),
            404
        );

        $trip->load(['route', 'ship']);
        $ticketTypes = TicketType::where('status', true)->orderBy('sort')->orderBy('id')->get();

        return view('booking.create', compact('trip', 'ticketTypes'));
    }

    public function store(Request $request, Trip $trip)
    {
        $ticketTypes = TicketType::where('status', true)->get()->keyBy('id');

        $data = $request->validate([
            'contact_name' => ['required','string','max:100'],
            'contact_phone' => ['required','string','max:30'],
            'contact_email' => ['nullable','email','max:150'],
            'note' => ['nullable','string','max:1000'],
            'ticket_quantities' => ['required','array'],
            'ticket_quantities.*' => ['nullable','integer','min:0','max:20'],
            'passengers' => ['required','array'],
            'passengers.*.name' => ['required','string','max:100'],
            'passengers.*.ticket_type_id' => ['required','integer'],
            'passengers.*.id_number' => ['nullable','string','max:30'],
            'passengers.*.birthday' => ['nullable','date'],
            'passengers.*.phone' => ['nullable','string','max:30'],
            'passengers.*.gender' => ['nullable','string','max:20'],
        ]);

        $quantities = [];
        $totalPassengers = 0;
        $totalAmount = 0;

        foreach ($data['ticket_quantities'] as $ticketTypeId => $quantity) {
            $quantity = (int) $quantity;
            if ($quantity <= 0) {
                continue;
            }

            if (!$ticketTypes->has((int) $ticketTypeId)) {
                return back()->withInput()->with('error', '票種資料無效。');
            }

            $ticketType = $ticketTypes->get((int) $ticketTypeId);
            $quantities[(int) $ticketTypeId] = $quantity;
            $totalPassengers += $quantity;
            $totalAmount += $quantity * (float) $ticketType->price;
        }

        if ($totalPassengers < 1) {
            return back()->withInput()->with('error', '請至少選擇 1 張票。');
        }

        if (count($data['passengers']) !== $totalPassengers) {
            return back()->withInput()->with('error', '旅客人數必須與購票數量相同。');
        }

        $passengerTypeCounts = [];
        foreach ($data['passengers'] as $passenger) {
            $typeId = (int) $passenger['ticket_type_id'];
            $passengerTypeCounts[$typeId] = ($passengerTypeCounts[$typeId] ?? 0) + 1;
        }

        foreach ($quantities as $typeId => $quantity) {
            if (($passengerTypeCounts[$typeId] ?? 0) !== $quantity) {
                return back()->withInput()->with('error', '旅客資料的票種數量與購票數量不一致。');
            }
        }

        $order = DB::transaction(function () use ($trip, $data, $quantities, $ticketTypes, $totalPassengers, $totalAmount) {
            $lockedTrip = Trip::whereKey($trip->id)->lockForUpdate()->firstOrFail();

            if (
                $lockedTrip->status !== 'open' ||
                ($lockedTrip->departure_date->isToday() === false && $lockedTrip->departure_date->isFuture() === false)
            ) {
                abort(409, '此航次目前無法訂位。');
            }

            $available = $lockedTrip->capacity - $lockedTrip->booked_count;
            if ($totalPassengers > $available) {
                abort(409, '此航次剩餘座位不足，目前剩餘 ' . max(0, $available) . ' 位。');
            }

            do {
                $orderNo = 'BT' . now()->format('ymdHis') . Str::upper(Str::random(4));
            } while (Order::where('order_no', $orderNo)->exists());

            $order = Order::create([
                'order_no' => $orderNo,
                'trip_id' => $lockedTrip->id,
                'contact_name' => $data['contact_name'],
                'contact_phone' => $data['contact_phone'],
                'contact_email' => $data['contact_email'] ?? null,
                'total_amount' => $totalAmount,
                'payment_status' => 'unpaid',
                'status' => 'pending',
                'note' => $data['note'] ?? null,
            ]);

            foreach ($quantities as $typeId => $quantity) {
                $price = (float) $ticketTypes->get($typeId)->price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'ticket_type_id' => $typeId,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'subtotal' => $price * $quantity,
                ]);
            }

            foreach ($data['passengers'] as $passenger) {
                OrderPassenger::create([
                    'order_id' => $order->id,
                    'ticket_type_id' => (int) $passenger['ticket_type_id'],
                    'name' => $passenger['name'],
                    'id_number' => $passenger['id_number'] ?? null,
                    'birthday' => $passenger['birthday'] ?? null,
                    'phone' => $passenger['phone'] ?? null,
                    'gender' => $passenger['gender'] ?? null,
                ]);
            }

            $lockedTrip->increment('booked_count', $totalPassengers);

            return $order;
        });

        return redirect()->route('booking.success', $order);
    }

    public function success(Order $order)
    {
        $order->load(['trip.route', 'trip.ship', 'items.ticketType', 'passengers.ticketType']);

        return view('booking.success', compact('order'));
    }
}
