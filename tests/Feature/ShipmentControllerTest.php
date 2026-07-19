<?php

use App\Jobs\SendShipmentCreatedEmail;
use App\Models\Shipment;
use App\Models\User;
use App\Services\ShipmentService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a dispatcher can successfully book a shipment', function () {
    // 1. Fake the Queue so emails aren't actually put into the DB jobs table or processed
    Queue::fake();

    // 2. Prepare our database state (Seeding an available driver)
    $driver = User::factory()->create([
        'role' => 'driver'
    ]);

    // 3. Make the POST request
    $payload = [
        'origin_address' => '123 Port Road, Houston, TX',
        'destination_address' => '456 Warehouse Blvd, Dallas, TX',
        'weight' => 20.0, // expected price: 10 + (20 * 1.5) = 40.00
        'customer_email' => 'customer@logiflow.com',
        "shipping_type" => "local"
    ];

    $response = $this->postJson('/api/shipments', $payload);

    // 4. Assert response is successful
    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'shipment' => [
                'id',
                'tracking_number',
                'price',
                'driver_id',
            ]
        ]);

    // 5. Assert the database has the expected record
    $this->assertDatabaseHas('shipments', [
        'origin_address' => '123 Port Road, Houston, TX',
        'destination_address' => '456 Warehouse Blvd, Dallas, TX',
        'price' => 40.00,
        'driver_id' => $driver->id,
    ]);

    // 6. Assert that our background job was pushed to the queue with the correct parameters
    Queue::assertPushed(SendShipmentCreatedEmail::class, function ($job) use ($payload) {
        return $job->customerEmail === $payload['customer_email'];
    });
});

test('it returns validation errors with invalid payload', function () {
    $response = $this->postJson('/api/shipments', []); // empty payload

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['origin_address', 'destination_address', 'weight', 'customer_email']);
});

test('it routes national shipments through the FedEx strategy', function () {
    Queue::fake();

    $payload = [
        'origin_address' => 'Houston, TX',
        'destination_address' => 'New York, NY',
        'weight' => 10.0, // FedEx: 25.00 base + (10 * 0.80) = 33.00
        'customer_email' => 'fedex-test@example.com',
        'shipping_type' => 'national',
    ];

    $response = $this->postJson('/api/shipments', $payload);

    $response->assertStatus(201);

    // Verify database record has correct price calculation and tracking prefix
    $this->assertDatabaseHas('shipments', [
        'price' => 33.00,
        'driver_id' => null, // FedEx shipments don't use internal driver resources
    ]);

    $shipment = Shipment::where('customer_email', 'not-existent-so-let-us-just-grab-latest')->latest()->first();
    
    // Assert tracking prefix matches the FedEx strategy output
    expect($response->json('shipment.tracking_number'))->toStartWith('FDX-');
});

test('it rolls back shipment creation if an unhandled error happens inside the service process', function () {
    // 1. Mock or intercept the factory or inject a payload that will intentionally fail after initialization
    $service = app(ShipmentService::class);

    // We pass a payload missing vital columns that aren't validated by FormRequest, or force a failure state
    // Let's create an unhandled scenario or use a try-catch test block:
    
    try {
        $service->createShipment([
            'shipping_type' => 'local',
            'weight' => 10.0,
            'origin_address' => '123 Test St',
            // Missing 'destination_address' entirely, which will trigger a Database Level Column Integrity Exception!
        ]);
    } catch (\Exception $e) {
        // Exception caught successfully
    }

    // 2. CRITICAL ASSERTION:
    // Even though the factory successfully calculated things and started processing,
    // the shipment row MUST NOT exist because the database transaction rolled it back completely.
    expect(Shipment::count())->toBe(0);
});
