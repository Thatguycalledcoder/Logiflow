<?php

use App\Jobs\SendShipmentCreatedEmail;
use App\Models\User;
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
