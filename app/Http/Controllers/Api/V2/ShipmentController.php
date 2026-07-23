<?php

namespace App\Http\Controllers\Api\V2;

use App\Events\ShipmentStatusUpdated;
use App\Models\Shipment;
use App\Http\Controllers\Api\V1\ShipmentController as V1ShipmentController;
use App\Http\Resources\Api\V2\ShipmentResource;
use App\Http\Requests\UpdateShipmentRequest;

class ShipmentController extends V1ShipmentController
{
    public function index()
    {
        $shipments = Shipment::with('driver')->get();
        return ShipmentResource::collection($shipments);
    }

    public function update(UpdateShipmentRequest $request, Shipment $shipment): ShipmentResource
    {   
        $validated = $request->validated();
        $shipment->update(['status' => $validated['status']]);

        //Dispatch the event (Non-blocking!)
        ShipmentStatusUpdated::dispatch($shipment);
        return new ShipmentResource($shipment->load('driver'));
    }
}