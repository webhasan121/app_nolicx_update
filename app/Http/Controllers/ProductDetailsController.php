<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSaveForLater;
use App\Models\UserTask;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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

        $recommendedProducts = $this->forYouProducts($request);




        $ratingCount = $product->comments->whereNotNull('rating')->count();
        $averageRating = $ratingCount
            ? round((float) $product->comments->whereNotNull('rating')->avg('rating'), 1)
            : 0;

        return Inertia::render('Products/Details', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'title' => $product->title,
                'slug' => $product->slug,
                'description' => $product->description,
                'thumbnail' => $product->thumbnail,
                'video' => $product->video,
                'video_url' => $this->videoUrl($product->video),
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
                'rating' => [
                    'average' => $averageRating,
                    'out_of_10' => $averageRating ? round($averageRating * 2, 1) : 0,
                    'count' => $ratingCount,
                ],
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
                'is_saved_for_later' => $request->user()
                    ? ProductSaveForLater::query()
                        ->where('user_id', $request->user()->id)
                        ->where('product_id', $product->id)
                        ->exists()
                    : false,
                'comments' => $product->comments
                    ->sortByDesc('created_at')
                    ->values()
                    ->map(function ($comment) {
                        $message = trim((string) ($comment->review ?: $comment->comments));

                        if ($comment->rating && $message === (string) $comment->rating) {
                            $message = '';
                        }

                        return [
                            'id' => $comment->id,
                            'user_id' => $comment->user_id,
                            'comments' => $message,
                            'rating' => $comment->rating,
                            'created_at_human' => $comment->created_at?->diffForHumans(),
                            'user' => [
                                'name' => $comment->user?->name,
                            ],
                        ];
                    }),
            ],
            'relatedProducts' => $relatedProducts,
            'recommendedProducts' => $recommendedProducts,
            'task' => $this->getTaskData($request),
        ]);
    }

    public function saveForLater(Request $request, $id, $slug)
    {
        $product = Product::query()
            ->where([
                'id' => (int) $id,
                'status' => 'Active',
                'belongs_to_type' => 'reseller',
            ])
            ->firstOrFail();

        $saved = ProductSaveForLater::query()->where([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
        ])->first();

        if ($saved) {
            $saved->delete();

            return response()->json([
                'saved' => false,
                'message' => 'Product removed from saved list',
            ]);
        }

        ProductSaveForLater::create([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
        ]);

        return response()->json([
            'saved' => true,
            'message' => 'Product saved for later',
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
        $currentTask = UserTask::where([
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
                UserTask::create([
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

        $currentTask = UserTask::where([
            'user_id' => $request->user()->id,
            'package_id' => $vip->package_id,
        ])->whereDate('created_at', today())->first();

        $lastTask = UserTask::where([
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

    private function forYouProducts(Request $request)
    {
        $columns = [
            'id',
            'name',
            'title',
            'slug',
            'thumbnail',
            'offer_type',
            'discount',
            'price',
            'unit',
        ];

        $savedIds = $request->user()
            ? ProductSaveForLater::query()
                ->where('user_id', $request->user()->id)
                ->latest()
                ->limit(20)
                ->pluck('product_id')
                ->values()
                ->all()
            : [];

        if ($request->user() && !$savedIds) {
            return collect();
        }

        $savedProducts = collect();

        if ($savedIds) {
            $savedProducts = Product::query()
                ->reseller()
                ->active()
                ->whereIn('id', $savedIds)
                ->get($columns)
                ->sortBy(fn($product) => array_search($product->id, $savedIds, true))
                ->values();
        }

        if ($request->user()) {
            return $savedProducts->take(20)->values();
        }

        $fallback = Product::query()
            ->reseller()
            ->active()
            ->home()
            ->when($savedProducts->isNotEmpty(), fn($query) => $query->whereNotIn('id', $savedProducts->pluck('id')))
            ->orderBy('vc')
            ->limit(max(0, 20 - $savedProducts->count()))
            ->get($columns);

        return $savedProducts->merge($fallback)->take(20)->values();
    }

    private function videoUrl(?string $video): ?string
    {
        if (empty($video)) {
            return null;
        }

        return Str::startsWith($video, ['http://', 'https://'])
            ? $video
            : asset('storage/' . $video);
    }
}
