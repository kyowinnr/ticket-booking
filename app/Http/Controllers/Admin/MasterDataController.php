<?php

namespace AppHttpControllersAdmin;

use AppHttpControllersController;
use AppModelsRoute as ShippingRoute;
use AppModelsShip;
use AppModelsTicketType;
use AppModelsTrip;

class MasterDataController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'routeCount' => ShippingRoute::count(),
            'shipCount' => Ship::count(),
            'ticketTypeCount' => TicketType::count(),
            'tripCount' => Trip::whereDate('departure_date', '>=', today())->count(),
        ]);
    }
}
