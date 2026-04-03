<?php

namespace App\Listeners;

use App\Events\PackagePurchaseComissionForReferred;
use App\Models\user_has_refs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;

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
        $vip = $event->vip; // get the vips data from vips table via event payload
        $vipUser = $vip->user; // get the user

        if ($vipUser->reference && $vipUser->created_at->diffInHours(Carbon::now()) <= 72) {
            // if user accept the reference and user registration won't over the 72 hours

            $ref = user_has_refs::query()->where(['ref' => $vipUser->reference])->first();
            if ($ref) {
                // ref exists 
            }
        }
    }
}
