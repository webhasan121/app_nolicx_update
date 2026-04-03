<?php

use Livewire\Volt\Component;
use App\Model\product;

new class extends Component {
    public $vid, $count;

    public function mount() 
    {
        $this->$count = product::where(['user_id' => $vid])->count();
    }
    
}; ?>

<div>
    {{$count}}
</div>
