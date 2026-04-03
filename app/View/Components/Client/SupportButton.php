<?php

namespace App\View\Components\Client;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SupportButton extends Component
{
    public string $whatsapp;

    /**
     * Create a new component instance.
     */
    public function __construct(string $whatsapp = null)
    {
        $this->whatsapp = $whatsapp ?? config('app.whatsapp_no');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.client.support-button');
    }
}
