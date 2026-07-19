<?php

namespace App\Contracts;

use App\Models\Shipment;

interface ShippingCarrierInterface
{
    /**
     * Calculate the rate based on weight.
     */
    public function calculateRate(float $weight): float;

    /**
     * Book the shipment with the provider and return a tracking number.
     */
    public function bookShipment(array $shipmentData, float $price): string;
}