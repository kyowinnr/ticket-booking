<?php

namespace Database\Seeders;

use App\Models\Route as ShippingRoute;
use App\Models\Ship;
use App\Models\TicketType;
use App\Models\Trip;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $outbound = ShippingRoute::updateOrCreate(
            ['departure_port' => '布袋港', 'arrival_port' => '澎湖'],
            ['name' => '布袋港 → 澎湖', 'status' => true, 'sort' => 1]
        );

        $return = ShippingRoute::updateOrCreate(
            ['departure_port' => '澎湖', 'arrival_port' => '布袋港'],
            ['name' => '澎湖 → 布袋港', 'status' => true, 'sort' => 2]
        );

        $ship = Ship::updateOrCreate(
            ['name' => '測試船 A'],
            ['capacity' => 300, 'status' => true, 'description' => '系統試用船舶']
        );

        $adult = TicketType::updateOrCreate(
            ['name' => '全票'],
            ['price' => 1000, 'description' => '測試票種', 'status' => true, 'sort' => 1]
        );

        $child = TicketType::updateOrCreate(
            ['name' => '兒童票'],
            ['price' => 500, 'description' => '測試票種', 'status' => true, 'sort' => 2]
        );

        foreach (range(0, 6) as $day) {
            $date = Carbon::today()->addDays($day);

            Trip::updateOrCreate(
                [
                    'ship_id' => $ship->id,
                    'departure_date' => $date->toDateString(),
                    'departure_time' => '08:30',
                ],
                [
                    'route_id' => $outbound->id,
                    'arrival_time' => '09:30',
                    'capacity' => 300,
                    'booked_count' => 0,
                    'status' => 'open',
                    'note' => '試用航次'
                ]
            );

            Trip::updateOrCreate(
                [
                    'ship_id' => $ship->id,
                    'departure_date' => $date->toDateString(),
                    'departure_time' => '15:30',
                ],
                [
                    'route_id' => $return->id,
                    'arrival_time' => '16:30',
                    'capacity' => 300,
                    'booked_count' => 0,
                    'status' => 'open',
                    'note' => '試用航次'
                ]
            );
        }
    }
}
