<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCommentLike;
use App\Models\Products_has_comments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    /**
     * @param App\Models\Product
     * @param comments
     * 
     * 
     * @return json
     */
    public function addCommentsToProduct(Request $req)
    {
        // $product = Product::findOrFAil($req->product_id);
        $req->validate(
            [
                'product_id' => ['required', 'integer', 'exists:products,id'],
                'comments' => ['required', 'string', 'max:500'],
                'rating' => ['required', 'numeric', 'min:1', 'max:5'],
                'images' => ['nullable', 'array', 'max:3'],
                'images.*' => ['file', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
            ]
        );

        try {
            DB::transaction(function ()
            use ($req) {
                $images = $this->storeCommentImages($req);
                $comments = new Products_has_comments();
                $comments->forceFill(
                    [
                        'product_id' => $req->product_id,
                        'user_id' => Auth::id(),
                        'comments' => $req->comments,
                        'review' => $req->comments,
                        'rating' => $req->integer('rating') ?: null,
                        'image' => $images[0] ?? null,
                        'images' => $images,
                        'approved' => true,
                    ]
                );
                $comments->save();
            });
            return response()->json(['success' => true, 'message' => 'Comment Added !'], 200);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 200);
        }
    }



    /**
     * route method
     */
    public function storeComment(Request $req)
    {
        // $product = Product::findOrFAil($req->product_id);
        $req->validate(
            [
                'product_id' => ['required', 'integer', 'exists:products,id'],
                'comments' => ['required', 'string', 'max:500'],
                'rating' => ['required', 'numeric', 'min:1', 'max:5'],
                'images' => ['nullable', 'array', 'max:3'],
                'images.*' => ['file', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
            ]
        );

        $comment = DB::transaction(function () use ($req) {
            $images = $this->storeCommentImages($req);
            $comments = new Products_has_comments();
            $comments->forceFill([
                'product_id' => $req->product_id,
                'user_id' => Auth::id(),
                'comments' => trim($req->comments),
                'review' => trim($req->comments),
                'rating' => $req->integer('rating'),
                'image' => $images[0] ?? null,
                'images' => $images,
                'approved' => true,
            ]);
            $comments->save();

            return $comments->load(['user:id,name', 'cartOrder:id,size', 'likes:id,products_has_comment_id,user_id']);
        });

        if ($req->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'comment added !',
                'comment' => $this->commentPayload($comment, $req),
            ]);
        }

        return redirect()->back()->with('success', 'comment added !');
    }

    public function updateComment(Request $req, $id)
    {
        $validated = $req->validate([
            'comments' => ['required', 'string', 'max:500'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $comment = Products_has_comments::query()->findOrFail($id);

        if ((int) $comment->user_id !== (int) Auth::id()) {
            abort(403);
        }

        $message = trim($validated['comments']);

        $comment->forceFill([
            'comments' => $message,
            'review' => $message,
            'rating' => $req->integer('rating') ?: $comment->rating,
        ])->save();

        return redirect()->back()->with('success', 'comment updated !');
    }

    public function destroyComment($id)
    {
        $comment = Products_has_comments::query()->findOrFail($id);
        $user = Auth::user();
        $canManage = $user && method_exists($user, 'can') && $user->can('users_manage');

        if ((int) $comment->user_id !== (int) Auth::id() && !$canManage) {
            abort(403);
        }

        $comment->delete();

        return redirect()->back();
    }

    public function toggleCommentLike($id)
    {
        $comment = Products_has_comments::query()->findOrFail($id);
        $like = ProductCommentLike::query()
            ->where('products_has_comment_id', $comment->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($like) {
            $like->delete();
        } else {
            ProductCommentLike::create([
                'products_has_comment_id' => $comment->id,
                'user_id' => Auth::id(),
            ]);
        }

        $count = ProductCommentLike::query()
            ->where('products_has_comment_id', $comment->id)
            ->count();

        $comment->forceFill(['like' => $count])->save();

        return response()->json([
            'liked' => !$like,
            'like' => $count,
        ]);
    }

    private function storeCommentImages(Request $request): array
    {
        if (!$request->hasFile('images')) {
            return [];
        }

        return collect($request->file('images'))
            ->filter()
            ->map(fn($image) => $image->store('product-comments', 'public'))
            ->values()
            ->all();
    }

    private function commentPayload(Products_has_comments $comment, Request $request): array
    {
        $message = trim((string) ($comment->review ?: $comment->comments));
        $isOwner = (int) $request->user()->id === (int) $comment->user_id;

        if ($comment->rating && $message === (string) $comment->rating) {
            $message = '';
        }

        return [
            'id' => $comment->id,
            'user_id' => $comment->user_id,
            'comments' => $message,
            'rating' => $comment->rating,
            'image' => $comment->image,
            'images' => collect($comment->images ?: ($comment->image ? [$comment->image] : []))->filter()->values(),
            'is_verified_purchase' => (bool) ($comment->is_verified_user || $comment->order_id),
            'variant_label' => $comment->cartOrder?->size ? 'Color Family: ' . $comment->cartOrder->size : null,
            'like' => (int) ($comment->likes->count() ?: $comment->like ?? 0),
            'liked_by_me' => false,
            'can_edit' => $isOwner,
            'can_delete' => $isOwner || (bool) $request->user()?->can('users_manage'),
            'created_at_date' => $comment->created_at?->format('d M Y'),
            'created_at_human' => $comment->created_at?->diffForHumans(),
            'user' => [
                'name' => $comment->user?->name,
            ],
        ];
    }
}
