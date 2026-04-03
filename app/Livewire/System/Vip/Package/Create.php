<?php

namespace App\Livewire\System\Vip\Package;

use App\Models\Package_pays;
use App\Models\Packages;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


#[layout('layouts.app')]
class Create extends Component
{
    #[validate('required')]
    public $name, $price, $coin, $m_coin, $countdown, $ref_owner_get_coin;
    public $description, $owner_get_coin, $pay_type, $pay_to,  $paymentOptions = array(
        ['pay_type' => '', 'pay_to' => '']
    );

    public function addPaymentOption()
    {
        $this->paymentOptions[] =
            ['pay_type' => '', 'pay_to' => ''];
    }

    public function removePaymentOption($index)
    {
        unset($this->paymentOptions[$index]);
        $this->paymentOptions = array_values($this->paymentOptions); // reindex    
    }

    public function store()
    {
        DB::transaction(function () {
            $packId = Packages::create(
                [
                    'name' => $this->name,
                    'slug' => Str::slug($this->name),
                    'price' => $this->price,
                    'countdown' => $this->countdown,
                    'status' => 1,
                    'coin' => $this->coin,
                    'm_coin' => $this->m_coin,
                    'ref_owner_get_coin' => $this->ref_owner_get_coin,
                    'owner_get_coin' => $this->owner_get_coin,
                    'description' => $this->description,
                ]
            );

            foreach ($this->paymentOptions as $key => $value) {
                Package_pays::create(
                    [
                        'package_id' => $packId->id,
                        'pay_type' => $value['pay_type'],
                        'pay_to' => $value['pay_to'],
                    ]
                );
            }

            $this->redirectIntended(route('system.vip.index'), true);
        });
    }


    public function render()
    {
        return view('livewire.system.vip.package.create');
    }
}
