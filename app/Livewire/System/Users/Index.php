<?php

namespace App\Livewire\System\Users;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;



#[layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    #[URL]
    public $search, $sd, $ed;

    public function mount()
    {
        $this->search = '';
        $this->sd = now()->subDays(30)->format('Y-m-d');
        $this->ed = now()->format('Y-m-d');
    }

    public function print()
    {
        $url = route('system.users.print-summery', [
            'search' => $this->search,
            'sd' => $this->sd,
            'ed' => $this->ed,
        ]);

        $this->dispatch('open-printable', ['url' => $url]);
    }

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
            })->orderBy('id', 'desc')->paginate(config('app.paginate'));
        }
        $widgets = $this->widgets();
        return view('livewire.system.users.index', compact('users', 'widgets'));
    }

    private function widgets() {
        $users = User::get();
        return [
            [ 'head' => 'Total', 'data' => $users->count() ],
            [ 'head' => 'Today', 'data' => $users->where('created_at', today())->count() ],
            [ 'head' => 'VIP', 'data' => $users->where('vip', '!=', '0')->count() ],
        ];
    }
}
