<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route as ShippingRoute;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index()
    {
        $routes = ShippingRoute::orderBy('sort')->orderBy('id')->paginate(20);
        return view('admin.routes.index', compact('routes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'=>['required','string','max:100'],
            'departure_port'=>['required','string','max:100'],
            'arrival_port'=>['required','string','max:100'],
        ]);
        $data['status']=$request->boolean('status');
        ShippingRoute::create($data);
        return back()->with('success','航線已新增');
    }

    public function update(Request $request, ShippingRoute $route)
    {
        $data=$request->validate([
            'name'=>['required','string','max:100'],
            'departure_port'=>['required','string','max:100'],
            'arrival_port'=>['required','string','max:100'],
        ]);
        $data['status']=$request->boolean('status');
        $route->update($data);
        return back()->with('success','航線已更新');
    }

    public function destroy(ShippingRoute $route)
    {
        if ($route->trips()->exists()) return back()->with('error','此航線已有航次，請改為停用。');
        $route->delete();
        return back()->with('success','航線已刪除');
    }
}
