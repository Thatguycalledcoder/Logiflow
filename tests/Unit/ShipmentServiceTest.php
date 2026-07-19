<?php

use App\Services\Shipping\Carriers\InternalFleetCarrier;
use App\Services\Shipping\Carriers\FedExCarrier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('InternalFleetCarrier calculates shipment price correctly based on weight', function () {
    $carrier = new InternalFleetCarrier();

    // Base rate: 10.00, Weight multiplier: 1.50
    // Expected: 10.00 + (10 * 1.50) = 25.00
    $price = $carrier->calculateRate(10.0);

    expect($price)->toBe(25.00);
});

test('FedExCarrier calculates shipment price correctly based on weight', function () {
    $carrier = new FedExCarrier();

    // Base rate: 25.00, Weight multiplier: 0.80
    // Expected: 25.00 + (10 * 0.80) = 33.00
    $price = $carrier->calculateRate(10.0);

    expect($price)->toBe(33.00);
});

test('InternalFleetCarrier throws an exception when no drivers are available', function () {
    $carrier = new InternalFleetCarrier();

    // Ensure our database has absolutely no drivers
    User::where('role', 'driver')->delete();

    expect(fn () => $carrier->bookShipment([], 25.00))
        ->toThrow(Exception::class, 'No local drivers available at this moment');
});