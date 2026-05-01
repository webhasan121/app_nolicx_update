<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Products_has_comments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'product_id' => ['nullable'],
            'product' => ['nullable'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $productKey = $request->input('product_id', $request->input('product'));
        $limit = (int) $request->input('limit', 20);

        $query = Products_has_comments::query()
            ->with('user:id,name')
            ->latest();

        if ($productKey) {
            $product = Product::query()
                ->where('id', $productKey)
                ->orWhere('slug', $productKey)
                ->firstOrFail();

            $query->where('product_id', $product->id);
        }

        $comments = $query
            ->limit($limit)
            ->get()
            ->map(fn (Products_has_comments $comment) => $this->commentPayload($comment))
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Comments fetched',
            'data' => $comments,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'comments' => ['required', 'string', 'max:500'],
        ]);

        $product = Product::query()
            ->where([
                'id' => $validated['product_id'],
                'status' => 'Active',
                'belongs_to_type' => 'reseller',
            ])
            ->firstOrFail();

        $comment = new Products_has_comments();
        $comment->forceFill([
            'product_id' => $product->id,
            'user_id' => $request->user()->id,
            'is_verified_user' => $request->user()->hasVerifiedEmail() ? 1 : 0,
            'comments' => $validated['comments'],
        ]);
        $comment->save();
        $comment->load('user:id,name');

        $this->clearProductCache($product);

        return response()->json([
            'success' => true,
            'message' => 'Comment added',
            'data' => $this->commentPayload($comment),
        ], 201);
    }

    public function destroy(Request $request, Products_has_comments $comment)
    {
        if ((int) $comment->user_id !== (int) $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can delete only your own comment',
            ], 403);
        }

        $product = Product::find($comment->product_id);
        $comment->delete();

        if ($product) {
            $this->clearProductCache($product);
        }

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted',
            'data' => null,
        ]);
    }

    private function commentPayload(Products_has_comments $comment): array
    {
        return [
            'id' => $comment->id,
            'product_id' => $comment->product_id,
            'user_id' => $comment->user_id,
            'comments' => $comment->comments,
            'approved' => (bool) $comment->approved,
            'like' => $comment->like,
            'unlike' => $comment->unlike,
            'created_at' => $comment->created_at?->toDateTimeString(),
            'created_at_human' => $comment->created_at?->diffForHumans(),
            'user' => [
                'name' => $comment->user?->name,
            ],
        ];
    }

    private function clearProductCache(Product $product): void
    {
        Cache::forget('api.product.details.' . $product->id);

        if ($product->slug) {
            Cache::forget('api.product.details.' . $product->slug);
        }
    }
}
