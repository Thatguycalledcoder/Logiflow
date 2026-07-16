<?php

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it loads the shipments list without triggering N+1 queries', function () {
    $drivers = User::factory()->count(3)->create(['role' => 'driver']);

    // 2. Create 10 shipments assigned to these drivers
    foreach ($drivers as $driver) {
        Shipment::factory()->count(3)->create([
            'driver_id' => $driver->id,
            'status' => 'pending',
        ]);
    }

    // Enable query logging to count the database queries
    DB::enableQueryLog();

    // 3. Hit our dashboard API
    $response = $this->getJson('/api/shipments');

    $response->assertStatus(200);

    // 4. Retrieve logged queries
    $queries = DB::getQueryLog();
    $queryCount = count($queries);

    // If we had lazy loading (N+1), it would be 1 (shipments) + 9 (drivers) = 10 queries.
    // With Eager Loading, it must be exactly 2 queries!
    expect($queryCount)->toBe(2);
});