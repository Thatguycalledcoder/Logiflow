<?php

namespace App\Services\Shipping;

use App\Contracts\ShippingCarrierInterface;
use App\Services\Shipping\Carriers\InternalFleetCarrier;
use App\Services\Shipping\Carriers\FedExCarrier;
use App\Services\Shipping\Carriers\DhlCarrier;
use InvalidArgumentException;

class ShippingCarrierFactory
{
    /**
     * Resolve the carrier strategy based on the shipping type.
     */
    public function make(string $type): ShippingCarrierInterface
    {
        return match ($type) {
            'local' => new InternalFleetCarrier(),
            'national' => new FedExCarrier(),
            'international' => new DhlCarrier(),
            default => throw new InvalidArgumentException("Unsupported shipping carrier type: {$type}"),
        };
    }
}