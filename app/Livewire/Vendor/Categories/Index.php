<?php

namespace App\Livewire\Vendor\Categories;

use App\HandleImageUpload;
use App\Models\category;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;


#[layout('layouts.app')]
class Index extends Component
{

    use HandleImageUpload;

    public $categories, $account, $targettedForEdit = [], $image;

    public function mount()
    {
        $this->account = auth()->user()->account_type();
        $this->getData();
    }

    #[On('refresh')]
    public function getData()
    {
        $this->categories = auth()->user()->myCategory;
    }

    public function destroy($id)
    {
        auth()->user()->myCategory()->find($id)->delete();
        $this->dispatch('refresh');
        $this->dispatch('success', 'Category deleted !');
    }

    public function update()
    {
        auth()->user()->myCategory()->find($this->targettedForEdit['id'])->update(
            [
                'name' => $this->targettedForEdit['name'],
                'image' => $this->handleImageUpload($this->image, 'categories', $this->targettedForEdit['image']),
            ]
        );
        $this->dispatch('refresh');
        $this->dispatch('success', 'Updated');
    }



    public function render()
    {
        return view('livewire.vendor.categories.index');
    }
}
