<?php

namespace App\Livewire\Pages;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use App\Models\Cart;
use App\Models\user_task;
use App\Models\vip;
use Carbon\Month;
use Illuminate\Console\View\Components\Task;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\Attributes\Title;


use function PHPUnit\Framework\isNull;

#[layout('layouts.user.app')]
class ProductsDetails extends Component
{
    use WithPagination;

    #[URL]
    public $id, $slug;

    public $product, $vips, $duration, $countdown = 0, $currentTaskTime, $taskType = null, $lastTask = null, $currentTask = null, $taskNotCompletYet = true;

    public $min = 0, $sec = 0, $package;

    public function mount()
    {
        // dd(intval(30 / 60));
        // dd(now()->shortLocaleMonth);
        $this->product = Product::where(['id' => intval($this->id), 'status' => 'Active', 'belongs_to_type' => 'reseller'])->first();
        // $this->product = Product::find($this->id);
        // if ($this->product->status != 'ac') {
        //     # code...
        // }
        $this->vips = auth()->user()?->subscription()?->active()->valid()->first();
        $this->taskType = $this->vips?->task_type;
        $this->package = $this->vips?->package;
        $this->countdown = $this->package?->countdown ?? 0;
        $this->getData();


        // $this->vips = vip::where(['user_id' => Auth::id(), 'status' => 1])->whereDate('valid_till', '>', today())->first();
        // dd($this->vips->task_type);
    }

    #[on('count-task')]
    public function countTask()
    {
        /**
         * this method run by client side livewire dispatch event
         * when event ('count-task) dispatched from client side
         */


        // if task found, and task time equal to package time
        // then set the coin to task
        // dd('tast');


        if ($this->currentTaskTime >= $this->duration && $this->taskNotCompletYet) {

            /**
             * if task is running and
             * running counter time same to package timet then
             * save the task reward and
             * make the value false to taskNotCompletYet, means task complete done
             */
            $this->currentTask->coin = $this->package?->coin; // reward added to task
            $this->currentTask?->save();

            auth()->user()->coin += $this->package?->coin; // added to user wallet
            auth()->user()->save();

            $this->taskNotCompletYet = $this->currentTask?->coin ? false : true;
        } else {

            /**
             * if task is running and
             * running counter smaller than the package duration time
             * duration time multiplied by 60
             */

            if ($this->currentTask && $this->taskNotCompletYet) {

                /**
                 * if already task get in database.
                 * increase the time
                 */

                $this->currentTask?->increment('time');
            }
            if (!$this->currentTask && $this->taskNotCompletYet) {

                /**
                 * else create one instance for first time
                 */

                user_task::create(
                    [
                        'user_id' => Auth::id(),
                        'package_id' => $this->package?->id,
                        'vip_id' => $this->vips->id,
                        'earn_by' => 'task',
                        'time' => 0,
                    ]
                );
            }
        }


        /**
         * call getData function to get the latest values
         */
        $this->getData();
    }



    public function getData()
    {
        if (Auth::check()) {

            $this->currentTask = user_task::where(['user_id' => auth()->user()->id, 'package_id' => $this->vips?->package_id])->whereDate('created_at', '=', today())->first();
            $this->lastTask = user_task::where(['user_id' => auth()->user()->id, 'package_id' => $this->vips?->package_id])->latest()->first();
            // dd(Carbon::parse($this->lastTask->created_at)->shortLocaleMonth == today()->shortLocaleMonth);
            $this->currentTaskTime = $this->currentTask?->time ?? 0;
            $this->taskNotCompletYet = $this->currentTask?->coin ? false : true;
            $this->duration = $this->package?->countdown * 60;

            /**
             * check if task type is montyly
             * and 
             * latest task belongs to this running month, then
             * make false the value of taskNotCompleteYet, means task already complete.
             */
            if ($this->taskType == 'monthly' && Carbon::parse($this->lastTask->created_at)->shortLocaleMonth == today()->shortLocaleMonth && $this->lastTask?->coin) {
                $this->taskNotCompletYet = false;
            }

            $min = intval($this->currentTaskTime / 60, 0);
            $sec = $this->currentTaskTime - ($min * 60);

            $this->min = $min < 9 ? "0" . $min : $min;
            $this->sec = $sec < 9 ? "0" . $sec : $sec;
        }
    }

    public function render()
    {
        $relatedProduct = Product::where(['category_id' => $this->product?->category_id, 'status' => 'Active', 'belongs_to_type' => 'reseller'])->paginate(10);
        return view('livewire.pages.products-details', compact('relatedProduct'));
    }
}
