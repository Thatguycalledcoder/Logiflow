<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Support\Str;
use Exception;

class ShipmentService
{
    /**
     * Calculate the price based on shipment weight.
     */
    public function calculatePrice(float $weight): float
    {
        $baseRate = 10.00;
        $weightCost = $weight * 1.50;
        
        return $baseRate + $weightCost;
    }

    /**
     * Find an available driver.
     */
    public function findAvailableDriver(): User
    {
        $driver = User::where('role', 'driver')->first();
        if (!$driver) {
            throw new Exception('No drivers available at this moment');
        }

        return $driver;
    }

    /**
     * Handle the full shipment creation process.
     */
    public function createShipment(array $data): Shipment
    {
        $price = $this->calculatePrice($data['weight']);
        $driver = $this->findAvailableDriver();

        return Shipment::create([
            'tracking_number' => 'LOGI-' . strtoupper(Str::random(10)),
            'status' => 'pending',
            'origin_address' => $data['origin_address'],
            'destination_address' => $data['destination_address'],
            'weight' => $data['weight'],
            'price' => $price,
            'driver_id' => $driver->id,
        ]);
    }
}