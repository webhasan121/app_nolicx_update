<?php

namespace App\Livewire\System\Users;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.print')]
class PrintSummery extends Component
{

    #[URL]
    public $search, $sd, $ed;


    public function render()
    {
        // use cache here 
        $users = User::query()->withoutAdmin()->orderBy('id', 'desc')->whereBetween('created_at', [$this->sd, Carbon::parse($this->ed)->endOfDay()])->paginate(config('app.paginate'));
        // $this->getData();

        if (!empty($this->search)) {
            // rider::where('name', 'like', '%' . $this->search . '%')->paginate(20);
            $users = User::where(function ($query) {
                $query->whereAny(['name', 'email', 'reference', 'id'], 'like', '%' . $this->search . "%")
                    ->orWhereHas('subscription.package', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . "%");
                    })
                    ->orWhereHas('myRef', function ($q) {
                        $q->where('ref', 'like', '%' . $this->search . "%");
                    });
            })->get();
        }

        return view('livewire.system.users.print-summery', compact('users'));
    }
}
