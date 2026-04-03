<?php

namespace App\Livewire\System\Report;


use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[layout('layouts.app')]
class Index extends Component
{
    #[URL]
    public $nav = 'Deposit', $sdate, $edate, $sid;
    private $model;

    public function updated()
    {
       
    }

    public function generateReport()
    {
        $this->validate([
            'sdate' => 'required|date',
            'edate' => 'required|date',
        ]);

        $params = [
            'sdate' => $this->sdate,
            'edate' => $this->edate,
        ];

        if (!empty($this->sid)) {
            $params['id'] = $this->sid;
        }

        $url = route('system.report.generate', array_merge(['nav' => $this->nav], $params));

        // open in new tab

        return redirect()->to($url);
    }
    public function render()
    {
        return view('livewire.system.report.index');
    }
}
