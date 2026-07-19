<?php

namespace App\Services\Shipping\Carriers;

use App\Contracts\ShippingCarrierInterface;
use App\Models\User;
use Illuminate\Support\Str;
use Exception;
use Override;

class InternalFleetCarrier implements ShippingCarrierInterface
{
    public function calculateRate(float $weight): float
    {
        return 10.00 + ($weight * 1.50);
    }

    public function bookShipment(array $shipmentData, float $price): string
    {
        $driver = User::where('role', 'driver')->first();
        if (!$driver) {
            throw new Exception(('No local drivers available at this moment'));
        }

        return 'LOGI-' . strtoupper(Str::random(10));
    }
}