<?php

use App\Models\User;
use App\Services\ShipmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it calculates shipment price correctly based on weight', function() {
    $service = new ShipmentService();

    // Base rate: 10.00, Weight multiplier: 1.50
    // Expected: 10.00 + (10 * 1.50) = 25.00
    $price = $service->calculatePrice(10.0);

    expect($price)->toBe(25.00);
});

test('it throws an exception when no drivers are available', function() {
    $service = new ShipmentService();

    // Ensure our database has absolutely no drivers
    User::where('role', 'driver')->delete();

    expect(fn () => $service->findAvailableDriver())
        ->toThrow(Exception::class, 'No drivers available at this moment');
});