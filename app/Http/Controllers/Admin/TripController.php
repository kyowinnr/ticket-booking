<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Route as ShippingRoute;
use App\Models\Ship;
use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
 public function index(Request $request){
  $query=Trip::with(['route','ship'])->orderBy('departure_date')->orderBy('departure_time');
  if($request->filled('date')) $query->whereDate('departure_date',$request->date);
  $trips=$query->paginate(30)->withQueryString();
  return view('admin.trips.index',['trips'=>$trips,'routes'=>ShippingRoute::where('status',true)->orderBy('sort')->get(),'ships'=>Ship::where('status',true)->orderBy('name')->get()]);
 }
 public function store(Request $request){
  $data=$request->validate([
   'route_id'=>['required','exists:routes,id'],'ship_id'=>['required','exists:ships,id'],
   'departure_date'=>['required','date'],'departure_time'=>['required','date_format:H:i'],
   'arrival_time'=>['nullable','date_format:H:i'],'capacity'=>['required','integer','min:1'],'note'=>['nullable','string']
  ]);
  $ship=Ship::findOrFail($data['ship_id']);
  if($data['capacity']>$ship->capacity) return back()->withInput()->with('error','航次可售座位不能超過船舶載客量 '.$ship->capacity.' 人。');
  $data['status']='open'; $data['booked_count']=0;
  Trip::create($data);
  return back()->with('success','航次已新增');
 }
 public function update(Request $request,Trip $trip){
  $data=$request->validate([
   'route_id'=>['required','exists:routes,id'],'ship_id'=>['required','exists:ships,id'],
   'departure_date'=>['required','date'],'departure_time'=>['required','date_format:H:i'],
   'arrival_time'=>['nullable','date_format:H:i'],'capacity'=>['required','integer','min:1'],'status'=>['required','in:open,closed,cancelled'],'note'=>['nullable','string']
  ]);
  if($data['capacity']<$trip->booked_count) return back()->with('error','可售座位不能低於目前已訂人數 '.$trip->booked_count.' 人。');
  $ship=Ship::findOrFail($data['ship_id']);
  if($data['capacity']>$ship->capacity) return back()->with('error','航次可售座位不能超過船舶載客量 '.$ship->capacity.' 人。');
  $trip->update($data);
  return back()->with('success','航次已更新');
 }
 public function destroy(Trip $trip){
  if($trip->orders()->whereIn('status',['pending','confirmed','completed'])->exists()) return back()->with('error','此航次已有訂單，不能刪除，請改為取消。');
  $trip->delete(); return back()->with('success','航次已刪除');
 }
}