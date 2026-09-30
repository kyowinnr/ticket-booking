<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['trip.route', 'trip.ship'])
            ->orderByDesc('id');

        if ($request->filled('order_no')) {
            $query->where('order_no', 'like', '%' . trim($request->order_no) . '%');
        }

        if ($request->filled('contact_phone')) {
            $query->where('contact_phone', 'like', '%' . trim($request->contact_phone) . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date')) {
            $query->whereHas('trip', fn ($q) => $q->whereDate('departure_date', $request->date));
        }

        $orders = $query->paginate(30)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load([
            'trip.route',
            'trip.ship',
            'items.ticketType',
            'passengers.ticketType',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,cancelled,completed'],
            'payment_status' => ['required', 'in:unpaid,paid,refunded'],
        ]);

        DB::transaction(function () use ($order, $data) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($lockedOrder->status !== 'cancelled' && $data['status'] === 'cancelled') {
                $trip = $lockedOrder->trip()->lockForUpdate()->firstOrFail();
                $passengerCount = $lockedOrder->passengers()->count();
                $trip->decrement('booked_count', min($passengerCount, $trip->booked_count));
            }

            if ($lockedOrder->status === 'cancelled' && $data['status'] !== 'cancelled') {
                $trip = $lockedOrder->trip()->lockForUpdate()->firstOrFail();
                $passengerCount = $lockedOrder->passengers()->count();
                $available = $trip->capacity - $trip->booked_count;

                if ($passengerCount > $available) {
                    abort(409, '無法恢復此訂單，航次剩餘座位不足。');
                }

                $trip->increment('booked_count', $passengerCount);
            }

            $lockedOrder->update($data);
        });

        return back()->with('success', '訂單狀態已更新');
    }
}
