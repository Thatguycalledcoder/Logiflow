<?php

namespace App\Services\Shipping\Carriers;

use App\Contracts\ShippingCarrierInterface;
use Illuminate\Support\Str;

class FedExCarrier implements ShippingCarrierInterface
{
    public function calculateRate(float $weight): float
    {
        // FedEx has a higher base rate but lower weight multiplier for domestic shipping
        return 25.00 + ($weight * 0.80);
    }

    public function bookShipment(array $shipmentData, float $price): string
    {
        // In a real application, you would use fetch from FedEx API
        return 'FDX-' . strtoupper(Str::random(12));
    }
}