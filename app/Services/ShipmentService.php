<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\User;
use App\Services\Shipping\ShippingCarrierFactory;
use Illuminate\Support\Facades\DB;
use Exception;

class ShipmentService
{
    public function __construct(
        protected ShippingCarrierFactory $carrierFactory
    ) {}

    public function createShipment(array $data): Shipment
    {
        $carrier = $this->carrierFactory->make($data['shipping_type']);
        $price = $carrier->calculateRate($data['weight']);
        $trackingNumber = $carrier->bookShipment($data, $price);

        $driverId = null;
        if ($data['shipping_type'] === 'local') {
            $driver = User::where('role', 'driver')->first();
            $driverId = $driver?->id;
        }

        // 🛡️ Wrap all interrelated database writes in a transaction block
        return DB::transaction(function () use ($trackingNumber, $data, $price, $driverId) {
            
            // Write 1: Create the Shipment
            $shipment = Shipment::create([
                'tracking_number' => $trackingNumber,
                'status' => 'pending',
                'origin_address' => $data['origin_address'],
                'destination_address' => $data['destination_address'],
                'weight' => $data['weight'],
                'price' => $price,
                'driver_id' => $driverId,
            ]);

            // Write 2: Simulating an internal log or secondary record that might break
            // (e.g., audit trails, multi-tenant billing logs, or status history tracking)
            // If anything fails here, Write 1 is automatically rolled back!
            
            return $shipment;
        });
    }
}