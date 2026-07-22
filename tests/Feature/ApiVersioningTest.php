<?php

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('V1 shipment endpoint returns the legacy flattened driver string structure', function () {
    $driver = User::factory()->create(['name' => 'Alice Smith', 'role' => 'driver']);
    Shipment::factory()->create([
        'driver_id' => $driver->id,
        'tracking_number' => 'LOGI-V1TEST123',
    ]);

    $response = $this->getJson('/api/v1/shipments');

    $response->assertStatus(200)
        ->assertJsonFragment([
            'tracking_number' => 'LOGI-V1TEST123',
            'driver_name' => 'Alice Smith', // Assert legacy field exists
        ])
        ->assertJsonMissing(['driver' => ['name' => 'Alice Smith']]); // Ensure new structure isn't leaking into V1
});

test('V2 shipment endpoint returns the modern nested driver object structure', function () {
    $driver = User::factory()->create(['name' => 'Bob Jones', 'role' => 'driver']);
    Shipment::factory()->create([
        'driver_id' => $driver->id,
        'tracking_number' => 'LOGI-V2TEST456',
    ]);

    $response = $this->getJson('/api/v2/shipments');
    $response->dump();

    $response->assertStatus(200)
        ->assertJsonFragment([
            'tracking_number' => 'LOGI-V2TEST456',
        ])
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'tracking_number',
                    'status',
                    'driver' => [
                        'id',
                        'name',
                        'email',
                    ]
                ]
            ]
        ]);
});