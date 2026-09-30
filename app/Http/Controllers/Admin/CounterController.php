<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CounterController extends Controller
{
    public function index(Request $request)
    {
        $order = null;

        if ($request->filled('order_no')) {
            $order = Order::where('order_no', trim($request->order_no))
                ->with(['trip.route', 'trip.ship', 'items.ticketType', 'passengers.ticketType'])
                ->first();
        }

        return view('admin.counter.index', compact('order'));
    }

    public function confirmPaid(Order $order)
    {
        DB::transaction(function () use ($order) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($lockedOrder->status === 'cancelled') {
                abort(409, '取消訂單不能直接收款。');
            }

            $lockedOrder->update([
                'status' => 'confirmed',
                'payment_status' => 'paid',
            ]);
        });

        return back()->with('success', '訂單已確認，並標記為已收款。');
    }

    public function confirm(Order $order)
    {
        DB::transaction(function () use ($order) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($lockedOrder->status === 'cancelled') {
                abort(409, '取消訂單不能直接確認。');
            }

            $lockedOrder->update(['status' => 'confirmed']);
        });

        return back()->with('success', '訂單已確認。');
    }

    public function paid(Order $order)
    {
        DB::transaction(function () use ($order) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($lockedOrder->status === 'cancelled') {
                abort(409, '取消訂單不能收款。');
            }

            $lockedOrder->update(['payment_status' => 'paid']);
        });

        return back()->with('success', '訂單已標記為已收款。');
    }
}
