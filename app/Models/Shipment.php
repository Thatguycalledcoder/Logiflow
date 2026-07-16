<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    /** @use HasFactory<\Database\Factories\ShipmentFactory> */
    use HasFactory;

    protected $fillable = [
        'tracking_number',
        'status',
        'origin_address',
        'destination_address',
        'weight',
        'price',
        'driver_id'
    ];

    public function driver(): BelongsTo 
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
