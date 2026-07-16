<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ShipmentController extends Controller
{
    public function store(Request $request) {
        $request->validate([
            'customer_email' => 'required|email',
            'weight' => 'required|numeric|min:0.1',
            'origin_address' => 'required|string',
            'destination_address' => 'required|string',
        ]);

        $baseRate = 10.00;
        $weightCost = $request->weight * 1.50;
        $totalPrice = $baseRate * $weightCost;

        $driver = User::where('role', 'driver')->first();
        if (!$driver) {
            return response()->json(['error' => 'No drivers available at this moment'], 422);
        }

        // 4. Manual record generation and saving
        $shipment = new Shipment();
        $shipment->tracking_number = 'LOGI-' . strtoupper(Str::random(10));
        $shipment->status = 'pending';
        $shipment->origin_address = $request->origin_address;
        $shipment->destination_address = $request->destination_address;
        $shipment->weight = $request->weight;
        $shipment->price = $totalPrice;
        $shipment->driver_id = $driver->id;
        $shipment->save();

        // 5. Sending an email synchronously (blocking the user's HTTP request!)
        Mail::raw("Your shipment {$shipment->tracking_number} has been created and assigned!", function ($message) use ($request) {
            $message->to($request->customer_email)
                    ->subject('Shipment Booked!');
        });

        // 6. Return response
        return response()->json([
            'message' => 'Shipment booked successfully!',
            'shipment' => $shipment
        ], 201);

    }
}
