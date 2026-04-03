<?php

namespace App\Livewire\System\Navigations;

use App\Models\Navigations;
use App\Models\Navigations_has_link;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;

#[layout('layouts.app')]
class Index extends Component
{
    // protected listener = [$refresh];

    // menu 
    public $menus, $selectedMenu = [], $renameMenu, $selectedMenuItems = [['name' => 'Give Item name', 'url' => 'Define Item Url']], $isOpenAddMenuForm = false;

    #[validate('required')]
    public $newMenu;
    // links 
    public $links, $newLink;

    public function mount()
    {
        $this->getInfo();
    }

    #[On('refresh')]
    public function getInfo()
    {
        $this->menus = Navigations::with('links')->get();
    }


    public function openAddMenuForm()
    {
        $this->isOpenAddMenuForm = !$this->isOpenAddMenuForm;
    }

    public function addNewMenu()
    {
        $this->reset('selectedMenuItems');
        $this->validate();
        if (!Navigations::where(['name' => $this->newMenu])->exists()) {
            # code...
            $this->selectedMenu = Navigations::create(
                [
                    'name' => $this->newMenu,
                ]
            );
            $this->reset('newMenu');
            if ($this->selectedMenu) {
                $this->renameMenu = $this->selectedMenu->name;

                $this->dispatch('refresh');
                $this->dispatch('open-modal', 'add-new-menu');
            }
        }
    }

    public function openMenu($id)
    {
        if (Navigations::find($id)) {
            $this->selectedMenu = Navigations::where(['id' => $id])->with('links')->first();
            $this->selectedMenuItems = $this->selectedMenu->links->toArray();
            $this->renameMenu = $this->selectedMenu->name;
            // dd($this->selectedMenuItems);
            $this->dispatch('open-modal', 'add-new-menu');
        }
    }

    public function addNewMenuItems()
    {
        // dd($this->selectedMenu->id);
        $this->selectedMenuItems[] = ['name' => '', 'url' => '', 'navigations_id' => $this->selectedMenu->id];
    }

    public function updateMenuItems()
    {
        Navigations_has_link::where(['navigations_id' => $this->selectedMenu->id])->delete();
        foreach ($this->selectedMenuItems as $key => $items) {
            Navigations_has_link::create($items);
        }
        $this->dispatch('refresh');
        $this->dispatch('close-modal', 'add-new-menu');
        $this->reset('selectedMenuItems');
    }

    public function destroyMenItems($index)
    {
        if (array_key_exists('id', $this->selectedMenuItems[$index])) {
            Navigations_has_link::find($this->selectedMenuItems[$index]['id'])->delete();
            // $this->dispatch('close-modal', 'add-new-menu');
            $this->getInfo();
            $this->selectedMenuItems = $this->selectedMenu->links->toArray();

            // $this->dispatch('refresh');
        }
    }


    public function updateRenameMenu()
    {
        Navigations::find($this->selectedMenu->id)->update(
            [
                'name' => $this->renameMenu,
            ]
        );
        $this->getInfo();
    }


    public function destroyMenu($id)
    {
        Navigations::find($id)->delete();
        Navigations_has_link::where(['navigations_id' => $id])->delete();
        $this->dispatch('refresh');
    }


    public function render()
    {
        return view('livewire.system.navigations.index');
    }
}
