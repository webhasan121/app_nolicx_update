<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\user_task;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProductDetailsController extends Controller
{
    public function show(Request $request, $id, $slug)
    {
        $product = Product::query()
            ->with([
                'category:id,name,slug',
                'attr:id,product_id,name,value',
                'showcase:id,product_id,image',
                'comments.user:id,name',
                'owner:id,name',
            ])
            ->where([
                'id' => (int) $id,
                'status' => 'Active',
                'belongs_to_type' => 'reseller',
            ])
            ->firstOrFail();



        $ownerShop = $product->owner?->resellerShop();

        $relatedProducts = Product::query()
            ->where([
                'category_id' => $product->category_id,
                'status' => 'Active',
                'belongs_to_type' => 'reseller',
            ])
            ->limit(10)
            ->get([
                'id',
                'name',
                'title',
                'slug',
                'thumbnail',
                'offer_type',
                'discount',
                'price',
                'unit',
            ]);

        $recommendedProducts = Product::query()
            ->reseller()
            ->active()
            ->home()
            ->orderBy('vc')
            ->limit(20)
            ->get([
                'id',
                'name',
                'title',
                'slug',
                'thumbnail',
                'offer_type',
                'discount',
                'price',
                'unit',
            ]);




        return Inertia::render('Products/Details', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'title' => $product->title,
                'slug' => $product->slug,
                'description' => $product->description,
                'thumbnail' => $product->thumbnail,
                'video' => $product->video,
                'video_url' => $product->video ? asset('storage/' . $product->video) : null,
                'offer_type' => $product->offer_type,
                'discount' => $product->discount,
                'price' => $product->price,
                'unit' => $product->unit,
                'shipping_note' => $product->shipping_note,
                'meta_title' => $product->meta_title,
                'seo_title' => $product->seo_title ?? null,
                'meta_description' => $product->meta_description,
                'keyword' => $product->keyword,
                'meta_thumbnail' => $product->meta_thumbnail,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                    'slug' => $product->category->slug,
                ] : null,
                'attr' => $product->attr ? [
                    'name' => $product->attr->name,
                    'value' => $product->attr->value,
                ] : null,
                'showcase' => $product->showcase->map(fn($image) => [
                    'id' => $image->id,
                    'image' => $image->image,
                ])->values(),
                'owner' => [
                    'id' => $product->owner?->id,
                    'name' => $product->owner?->name,
                    'shop' => $ownerShop ? [
                        'id' => $ownerShop->id,
                        'shop_name_en' => $ownerShop->shop_name_en,
                        'address' => $ownerShop->address,
                        'phone' => $ownerShop->phone,
                    ] : null,
                ],
                'comments' => $product->comments
                    ->sortByDesc('created_at')
                    ->values()
                    ->map(fn($comment) => [
                        'id' => $comment->id,
                        'user_id' => $comment->user_id,
                        'comments' => $comment->comments,
                        'created_at_human' => $comment->created_at?->diffForHumans(),
                        'user' => [
                            'name' => $comment->user?->name,
                        ],
                    ]),
            ],
            'relatedProducts' => $relatedProducts,
            'recommendedProducts' => $recommendedProducts,
            'task' => $this->getTaskData($request),
        ]);
    }

    public function countTask(Request $request, $id, $slug)
    {
        Product::query()
            ->where([
                'id' => (int) $id,
                'status' => 'Active',
                'belongs_to_type' => 'reseller',
            ])
            ->firstOrFail();

        $taskData = $this->getTaskData($request);

        if (!$taskData['enabled']) {
            return response()->json($taskData);
        }

        $vip = $request->user()->subscription()->active()->valid()->first();
        $package = $vip?->package;
        $duration = ($package?->countdown ?? 0) * 60;
        $currentTask = user_task::where([
            'user_id' => $request->user()->id,
            'package_id' => $vip?->package_id,
        ])->whereDate('created_at', today())->first();

        if (($currentTask?->time ?? 0) >= $duration && $taskData['task_not_complete_yet']) {
            $currentTask->coin = $package?->coin;
            $currentTask->save();

            $user = $request->user();
            $user->coin += $package?->coin;
            $user->save();
        } else {
            if ($currentTask && $taskData['task_not_complete_yet']) {
                $currentTask->increment('time');
            }

            if (!$currentTask && $taskData['task_not_complete_yet']) {
                user_task::create([
                    'user_id' => Auth::id(),
                    'package_id' => $package?->id,
                    'vip_id' => $vip?->id,
                    'earn_by' => 'task',
                    'time' => 0,
                ]);
            }
        }

        return response()->json($this->getTaskData($request));
    }

    private function getTaskData(Request $request): array
    {
        if (!$request->user()) {
            return $this->emptyTaskData();
        }

        $vip = $request->user()->subscription()->active()->valid()->first();
        $package = $vip?->package;

        if (!$vip || !$package) {
            return $this->emptyTaskData();
        }

        $currentTask = user_task::where([
            'user_id' => $request->user()->id,
            'package_id' => $vip->package_id,
        ])->whereDate('created_at', today())->first();

        $lastTask = user_task::where([
            'user_id' => $request->user()->id,
            'package_id' => $vip->package_id,
        ])->latest()->first();

        $currentTaskTime = $currentTask?->time ?? 0;
        $taskNotCompletYet = $currentTask?->coin ? false : true;

        if (
            $vip->task_type === 'monthly' &&
            $lastTask?->created_at &&
            Carbon::parse($lastTask->created_at)->shortLocaleMonth === today()->shortLocaleMonth &&
            $lastTask?->coin
        ) {
            $taskNotCompletYet = false;
        }

        $min = (int) floor($currentTaskTime / 60);
        $sec = $currentTaskTime - ($min * 60);

        return [
            'enabled' => true,
            'countdown' => (int) ($package->countdown ?? 0),
            'current_time' => $currentTaskTime,
            'duration' => (int) (($package->countdown ?? 0) * 60),
            'task_type' => $vip->task_type,
            'task_not_complete_yet' => $taskNotCompletYet,
            'min' => $min < 10 ? '0' . $min : (string) $min,
            'sec' => $sec < 10 ? '0' . $sec : (string) $sec,
        ];
    }

    private function emptyTaskData(): array
    {
        return [
            'enabled' => false,
            'countdown' => 0,
            'current_time' => 0,
            'duration' => 0,
            'task_type' => null,
            'task_not_complete_yet' => false,
            'min' => '00',
            'sec' => '00',
        ];
    }
}
