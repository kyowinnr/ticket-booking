<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Route as ShippingRoute;
use App\Models\Ship;
use App\Models\TicketType;
use App\Models\Trip;
class MasterDataController extends Controller {
 public function index(){return view('admin.dashboard',['routeCount'=>ShippingRoute::count(),'shipCount'=>Ship::count(),'ticketTypeCount'=>TicketType::count(),'tripCount'=>Trip::whereDate('departure_date','>=',today())->count()]);}
}