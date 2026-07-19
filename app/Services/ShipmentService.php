<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\User;
use App\Services\Shipping\ShippingCarrierFactory;
use Illuminate\Support\Str;
use Exception;

class ShipmentService
{
    public function __construct(
        protected ShippingCarrierFactory $carrierFactory
    ) {}

    /**
     * Handle the full shipment creation process.
     */
    public function createShipment(array $data): Shipment
    {
        // 1. Resolve the strategy dynamically using the factory (e.g., 'local', 'national', 'international')
        // We will expect 'shipping_type' to pass validation in our Form Request next
        $carrier = $this->carrierFactory->make($data['shipping_type']);

        // 2. Delegate the calculation and tracking number generation to the strategy
        $price = $carrier->calculateRate($data['weight']);
        $trackingNumber = $carrier->bookShipment($data, $price);

        // 3. If it's a local carrier, we can safely assign an internal driver id
        $driverId = null;
        if ($data['shipping_type'] === 'local') {
            $driver = User::where('role', 'driver')->first();
            $driverId = $driver?->id;
        }

        return Shipment::create([
            'tracking_number' => $trackingNumber,
            'status' => 'pending',
            'origin_address' => $data['origin_address'],
            'destination_address' => $data['destination_address'],
            'weight' => $data['weight'],
            'price' => $price,
            'driver_id' => $driverId,
        ]);
    }
}