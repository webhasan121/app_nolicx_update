<?php

namespace App\Listeners;

use App\Events\PackagePurchaseComissionForReferred;
use App\Support\VipReferralCommission;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPackagePurchaseComissionForReferred
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
    public function handle(PackagePurchaseComissionForReferred $event): void
    {
        VipReferralCommission::award($event->vip);
    }
}
