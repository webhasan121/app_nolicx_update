<?php

namespace App\Livewire\System\Settings\Pages;

use App\HandleImageUpload;
use App\Models\page_settings;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Url;

#[layout('layouts.app')]
class Create extends Component
{
    use WithFileUploads, HandleImageUpload;
    #[URL]
    public $page, $id;

    #[Validate]
    public $name, $slug, $title, $keyword, $description, $content, $thumbnail;

    public function updated($property)
    {
        if ($property == 'name') {
            $this->slug = Str::slug($this->name);
        }
    }

    public function mount()
    {
        if ($this->page) {
            $data = page_settings::where(['slug' => $this->page])->first();
            if ($data) {
                $this->id = $data->id;
                $this->name = $data->name;
                $this->title = $data->title;
                $this->slug = $data->slug;
                $this->keyword = $data->keyword;
                $this->content = $data->content;
                $this->description = $data->description;
                $this->thumbnail = $data->thumbnail;
                $this->page = $data->slug;
            }
        }
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
            ],
            'slug' => [
                'required',
                ValidationRule::unique(page_settings::class)->ignore($this->id),
            ],
        ];
    }

    public function createPage()
    {
        $this->validate();
        try {

            if ($this->page && page_settings::where('slug', '=', $this->page)->exists()) {
                page_settings::where('slug', '=', $this->page)->update(
                    [
                        'slug' => $this->slug,
                        'name' => $this->name,
                        'title' => $this->title,
                        'keyword' => $this->keyword,
                        'description' => $this->description,
                        'content' => $this->content,
                        'thumbnail' => $this->handleImageUpload($this->thumbnail, 'pages', '')
                    ]
                );
            } else {

                $id = page_settings::create(
                    [
                        'slug' => $this->slug,
                        'name' => $this->name,
                        'title' => $this->title,
                        'keyword' => $this->keyword,
                        'description' => $this->description,
                        'content' => $this->content,
                        'thumbnail' => $this->handleImageUpload($this->thumbnail, 'pages', '')
                    ]
                );
                $this->page = $id->slug;
                $this->id = $id->id;
            }

            $this->dispatch('success', 'Saved !');
        } catch (\Throwable $th) {
            $this->dispatch('error', $th->getMessage());
            //throw $th;
        }
    }

    public function deletePage($id)
    {
        if ($pages = page_settings::findOrFail($id)) {
            $pages->delete();
            $this->dispatch('success', 'Page Deleted !');
        }
    }

    public function render()
    {
        $pages = page_settings::all();
        return view('livewire.system.settings.pages.create', compact('pages'));
    }
}
