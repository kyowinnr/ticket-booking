<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketType;
use Illuminate\Http\Request;

class TicketTypeController extends Controller
{
    public function index()
    {
        $ticketTypes = TicketType::orderBy('sort')->orderBy('id')->paginate(20);
        return view('admin.ticket-types.index', compact('ticketTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'price' => ['required','numeric','min:0'],
            'description' => ['nullable','string'],
        ]);
        $data['status'] = $request->boolean('status');
        TicketType::create($data);
        return back()->with('success', '票種已新增');
    }

    public function update(Request $request, TicketType $ticketType)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'price' => ['required','numeric','min:0'],
            'description' => ['nullable','string'],
        ]);
        $data['status'] = $request->boolean('status');
        $ticketType->update($data);
        return back()->with('success', '票種已更新');
    }

    public function destroy(TicketType $ticketType)
    {
        if ($ticketType->orderItems()->exists()) {
            return back()->with('error', '此票種已有訂單資料，不能刪除，請改為停用。');
        }
        $ticketType->delete();
        return back()->with('success', '票種已刪除');
    }
}
