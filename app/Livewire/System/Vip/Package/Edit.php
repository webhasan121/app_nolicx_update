<?php

namespace App\Livewire\System\Vip\Package;

use App\Models\Package_pays;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Packages;
use Illuminate\Support\Str;

#[layout('layouts.app')]
class Edit extends Component
{
    public $packages, $data, $payoption, $st = '';

    public function mount(Packages $packages)
    {
        // dd($this->packages);
        $this->data = $this->packages->toArray();
        $this->payoption = $this->packages->payOption->toArray();
    }

    public function updated($property)
    {
        if (Str::startsWith($property, 'data')) {
            Packages::find($this->packages->id)->update($this->data);
        }
    }


    public function addPaymentOption()
    {
        $this->payoption[] = ['pay_to' => '', 'pay_by' => ''];
    }

    public function removePaymentOption($index)
    {
        // dd($index);
        $this->validate(
            [
                'data.name' => 'required',
                'data.coin' => 'required',
            ]
        );
        unset($this->payoption[$index]);
        // dd($this->payoption[$index]['pay_to']);
    }

    public function store()
    {
        Package_pays::where(['package_id' => $this->packages->id])->delete();
        foreach ($this->payoption as $key => $value) {
            if (!empty($value['pay_type']) && !empty($value['pay_to'])) {
                Package_pays::create(
                    [
                        'package_id' => $this->packages->id,
                        'pay_type' => $value['pay_type'],
                        'pay_to' => $value['pay_to'],
                    ]
                );
            }
        }
        $this->dispatch('success', "Updated !");
    }



    public function render()
    {
        return view('livewire.system.vip.package.edit');
    }
}
