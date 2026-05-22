<?php

namespace App\Listeners;

use App\Events\ProductComissions;
use App\Http\Controllers\ProductComissionController;
use Illuminate\Support\Facades\Log;

class TakeProductComissions
{
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
    public function handle(ProductComissions $event): void
    {
        if ($event->data) {
            ProductComissionController::dispatchProductComissionsListeners($event->data->id);
        }
    }


    /**
     * Handle a job failure.
     */
    public function failed(ProductComissions $event, \Throwable $exception): void
    {
        Log::error($exception);
        throw $exception;
    }
}
