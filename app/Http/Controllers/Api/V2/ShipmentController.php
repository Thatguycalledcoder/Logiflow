<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\Shipment;
use App\Http\Controllers\Api\V1\ShipmentController as V1ShipmentController;
use App\Http\Resources\Api\V2\ShipmentResource;

class ShipmentController extends V1ShipmentController
{
    public function index()
    {
        $shipments = Shipment::with('driver')->get();
        return ShipmentResource::collection($shipments);
    }
}