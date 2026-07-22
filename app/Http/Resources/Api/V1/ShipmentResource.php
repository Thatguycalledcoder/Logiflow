<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'tracking_number' => $this->tracking_number,
            'status' => $this->status,
            'driver_name' => $this->driver->name ?? 'Unassigned', // Flattened legacy string
        ];
    }
}
