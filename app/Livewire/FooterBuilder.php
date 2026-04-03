<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FooterLayout;

#[layout('layouts.app')]
class FooterBuilder extends Component
{
    public $layout = [
        'sections' => []
    ];

    public function mount()
    {
        $footer = FooterLayout::where('name', 'default')->first();
        if ($footer) {
            $this->layout = json_decode($footer->layout, true);
        }
    }

    public function addSection()
    {
        $this->layout['sections'][] = [
            'title' => 'New Section',
            'columns' => [
                ['widgets' => []]
            ]
        ];
    }

    public function addColumn($sIndex)
    {
        $this->layout['sections'][$sIndex]['columns'][] = ['widgets' => []];
    }

    public function addWidget($sIndex, $cIndex, $type = 'text')
    {
        $this->layout['sections'][$sIndex]['columns'][$cIndex]['widgets'][] = [
            'type' => $type,
            'content' => '',
            'label' => '',
            'url' => '',
            'icon' => ''
        ];
    }

    public function save()
    {
        FooterLayout::updateOrCreate(
            ['name' => 'default'],
            ['layout' => json_encode($this->layout)]
        );

        session()->flash('success', 'Footer layout saved!');
    }
    public function render()
    {
        return view('livewire.footer-builder');
    }
}
