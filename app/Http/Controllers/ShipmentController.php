<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShipmentRequest;
use App\Jobs\SendShipmentCreatedEmail;
use App\Services\ShipmentService;
use Exception;
use Illuminate\Http\JsonResponse;

class ShipmentController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService
    ) {}
    
    public function store(StoreShipmentRequest $request): JsonResponse {
        try {
            $shipment = $this->shipmentService->createShipment($request->validated());
            SendShipmentCreatedEmail::dispatch($shipment, $request->customer_email);

            return response()->json([
                'message' => 'Shipment booked successfully!',
                'shipment' => $shipment
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
