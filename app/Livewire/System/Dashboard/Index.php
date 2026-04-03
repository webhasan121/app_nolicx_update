<?php

namespace App\Livewire\System\Dashboard;

use Livewire\Component;
use App\Support\SystemDashboardOverview;

class Index extends Component
{
    private $userCount = 0, $vd = 0, $avd = 0, $rs = 0, $ars = 0, $ri = 0, $ari = 0, $adm = 0, $vp = 0, $cat = 0;


    public function getOverview()
    {
        $overview = SystemDashboardOverview::get();

        $this->userCount = $overview['userCount'];
        $this->vd = $overview['vd'];
        $this->avd = $overview['avd'];
        $this->rs = $overview['rs'];
        $this->ars = $overview['ars'];
        $this->ri = $overview['ri'];
        $this->ari = $overview['ari'];
        $this->adm = $overview['adm'];
        $this->vp = $overview['vp'];
        $this->cat = $overview['cat'];
    }
    public function render()
    {
        return view(
            'livewire.system.dashboard.index',
            [
                'userCount' => $this->userCount,
                'vd' => $this->vd,
                'avd' => $this->avd,
                'rs' => $this->rs,
                'ars' => $this->ars,
                'ri' => $this->ri,
                'ari' => $this->ari,
                'adm' => $this->adm,
                'vp' => $this->vp,
                'cat' => $this->cat,

            ]
        );
    }
}
