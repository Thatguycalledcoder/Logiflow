<?php

namespace App\Services\Shipping\Carriers;

use App\Contracts\ShippingCarrierInterface;
use Illuminate\Support\Str;

class DhlCarrier implements ShippingCarrierInterface
{
    public function calculateRate(float $weight): float
    {
        // DHL handles international, high base rate + customs handling fee simulations
        return 50.00 + ($weight * 2.10);
    }

    public function bookShipment(array $shipmentData, float $price): string
    {
        // Simulate international booking API response
        return 'DHL-' . strtoupper(Str::random(10));
    }
}