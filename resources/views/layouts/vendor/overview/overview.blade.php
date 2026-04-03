<?php 
use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\order;

// new class extends Component
// {
//     public $p, $tp, $ca, $tor, $por;

//     public function mount()
//     {
//     }
// }

$ac = auth()->user()->account_type();
// dd( auth()->user()->myProducts()->count());
$p = auth()->user()->myProducts()->where(['belongs_to_type' => $ac])?->count();
$por = auth()->user()->orderToMe()->where(['belongs_to_type' => $ac, 'status' => 'Confirm'])?->sum('total');
// $tp = auth()->user()->myProducts()->Trashed()?->count();

?>
<p class="mb-2 text-xs">
    Overall Details
</p>
<x-dashboard.overview.section>
    <x-dashboard.overview.div>
        <x-slot name="title">
            Products
        </x-slot>
        <x-slot name="content">

            <div>{{$p ?? "0"}}</div>

        </x-slot>
    </x-dashboard.overview.div>

    <x-dashboard.overview.div>
        <x-slot name="title">
            Sales
        </x-slot>
        <x-slot name="content">

            <div>
                ৳ {{$por ?? "0"}}
            </div>

        </x-slot>
    </x-dashboard.overview.div>

</x-dashboard.overview.section>
<hr class="my-2" />