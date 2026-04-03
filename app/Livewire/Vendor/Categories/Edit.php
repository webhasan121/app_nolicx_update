<?php

namespace App\Livewire\Vendor\Categories;

use App\HandleImageUpload;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

#[layout('layouts.app')]
class Edit extends Component
{
    use HandleImageUpload, WithFileUploads;

    #[URL]
    public $cat;

    public $targettedForEdit = [], $image;

    public function mount()
    {
        $this->targettedForEdit = auth()->user()->myCategory()->find($this->cat)->toArray();
        // dd($this->targettedForEdit);
    }

    public function update()
    {
        auth()->user()->myCategory()->find($this->cat)->update(
            [
                'name' => $this->targettedForEdit['name'],
                'slug' => Str::slug($this->targettedForEdit['name']),
                'image' => $this->handleImageUpload($this->image, 'categories', $this->targettedForEdit['image']),
            ]
        );
        $this->redirectIntended(route('vendor.category.view'));
    }

    public function render()
    {
        return view('livewire.vendor.categories.edit');
    }
}
