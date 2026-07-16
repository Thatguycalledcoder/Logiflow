<?php

namespace App\Jobs;

use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendShipmentCreatedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Shipment $shipment,
        public string $customerEmail
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::raw("Your shipment {$this->shipment->tracking_number} has been created and assigned!", function ($message) {
            $message->to($this->customerEmail)
                    ->subject('Shipment Booked!');
        });
    }
}
