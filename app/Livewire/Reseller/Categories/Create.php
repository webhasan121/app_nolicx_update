<?php

namespace App\Livewire\Reseller\Categories;

use App\HandleImageUpload;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\Category;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


#[layout('layouts.app')]
class Create extends Component
{

    use WithFileUploads, HandleImageUpload;

    #[validate]
    public $name, $image;

    // protected refresh listeners
    public $parent_id, $categories = [], $slug, $account;
    public function updated($pro)
    {
        if ($pro == 'name') {
            $this->slug = Str::slug($this->name);
        }
        // $this->getData();
    }


    public function mount()
    {
        $this->account = auth()->user()->isVendor() ? 'vendor' : 'reseller';
        // $this->categories = Category::whereNull('belongs_to')
        //     ->with(['children' => function ($query) {
        //         $query->orderBy('name');
        //     }, 'user'])
        //     ->orderBy('name')
        //     ->get();
        $this->categories = category::getAll();
        // dd($this->account);
    }

    public function getData()
    {
        $this->categories = category::getAll();

        if (!auth()->user()->can('category_add')) {
            $this->dispatch('warning', "You are not able to add category. ");
        }
    }


    protected function rules()
    {
        return [
            'name' => 'required|unique:categories,name|max:50',
            'image' => 'nullable|max:100',
            'parent_id' => 'nullable|exists:categories,id',
            'slug' => 'required|max:100|unique:categories,slug',
        ];
    }
    // 'slug' => 'nullable|max:100|unique:categories,slug,' . $this->id . ',id',
    public function save()
    {
        if (!auth()->user()->can('category_add')) {
            $this->dispatch('warning', "You are not able to add category. ");
            return;
        }

        $this->validate();
        category::create(
            [
                'name' => $this->name,
                'slug' => $this->slug ?: Str::slug($this->name),
                'image' => $this->handleImageUpload($this->image, 'categories', null),
                'user_id' => Auth::id(),
                'belongs_to' => $this->parent_id ?: null,
            ]
        );
        $this->reset('name', 'image', 'slug');
        $this->getData();
        $this->dispatch('added');

        // $this->dispatch('close-modal', 'category-create-modal');
        // $this->dispatch('success', 'Category Created');
    }

    public function render()
    {
        return view('livewire.reseller.categories.create');
    }
}
