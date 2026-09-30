<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ship;
use Illuminate\Http\Request;

class ShipController extends Controller
{
    public function index()
    {
        $ships=Ship::orderBy('id')->paginate(20);
        return view('admin.ships.index', compact('ships'));
    }

    public function store(Request $request)
    {
        $data=$request->validate(['name'=>['required','string','max:100'],'capacity'=>['required','integer','min:1'],'description'=>['nullable','string']]);
        $data['status']=$request->boolean('status');
        Ship::create($data);
        return back()->with('success','船舶已新增');
    }

    public function update(Request $request, Ship $ship)
    {
        $data=$request->validate(['name'=>['required','string','max:100'],'capacity'=>['required','integer','min:1'],'description'=>['nullable','string']]);
        $data['status']=$request->boolean('status');
        $ship->update($data);
        return back()->with('success','船舶已更新');
    }

    public function destroy(Ship $ship)
    {
        if ($ship->trips()->exists()) return back()->with('error','此船舶已有航次，請改為停用。');
        $ship->delete();
        return back()->with('success','船舶已刪除');
    }
}
