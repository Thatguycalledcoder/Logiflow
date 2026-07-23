<?php

namespace App\Listeners;

use App\Events\ShipmentStatusUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendCustomerShipmentNotification implements ShouldQueue
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ShipmentStatusUpdated $event): void
    {
        $shipment = $event->shipment;
        Log::info("Customer notified for shipment #{$shipment->id}");
    }
}
