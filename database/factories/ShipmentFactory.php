<?php

namespace Database\Factories;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Shipment>
 */
class ShipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $weight = fake()->randomFloat(2, 1, 100);

        return [
            'tracking_number' => strtoupper(Str::random(12)),
            'status' => fake()->randomElement([
                'pending',
                'in_transit',
                'delivered',
            ]),
            'origin_address' => fake()->streetAddress() . ', ' . fake()->city(),
            'destination_address' => fake()->streetAddress() . ', ' . fake()->city(),
            'weight' => $weight,
            'price' => 10 + ($weight * 1.5),
            'driver_id' => User::factory()->state([
                'role' => 'driver',
            ]),
        ];
    }
}
