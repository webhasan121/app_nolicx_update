<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RefController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $find = trim((string) $request->query('find', ''));
        $baseQuery = $this->refUsersQuery($request);
        $query = (clone $baseQuery);

        if ($find !== '') {
            $this->applySearch($query, $find);
        }

        $totalRefUsers = (clone $baseQuery)->count();
        $refUsers = $query->paginate(config('app.paginate'))->withQueryString();

        return Inertia::render('User/Refs', [
            'filters' => [
                'find' => $find,
            ],
            'refUsers' => [
                'data' => $refUsers->getCollection()->map(fn($refUser) => $this->refUserPayload($refUser))->values()->all(),
                'links' => collect($refUsers->linkCollection())->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => strip_tags($link['label']),
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'from' => $refUsers->firstItem(),
                'to' => $refUsers->lastItem(),
                'total' => $refUsers->total(),
            ],
            'refOwnerName' => $user->getReffOwner?->owner?->name ?? 'User Not Found',
            'totalRefUsers' => $totalRefUsers,
            'printUrl' => route('user.ref.print', [
                'find' => $find,
            ]),
        ]);
    }

    public function print(Request $request)
    {
        $find = trim((string) $request->query('find', ''));
        $query = $this->refUsersQuery($request);

        if ($find !== '') {
            $this->applySearch($query, $find);
        }

        $refUsers = $query
            ->get()
            ->map(fn($refUser) => $this->refUserPayload($refUser))
            ->values()
            ->all();

        return Inertia::render('User/Refs/Print', [
            'filters' => [
                'find' => $find,
            ],
            'refUsers' => $refUsers,
        ]);
    }

    private function refUsersQuery(Request $request)
    {
        $ref = $request->user()->myRef?->ref;

        return User::query()
            ->when(
                $ref,
                fn($query) => $query->where('reference', $ref),
                fn($query) => $query->whereRaw('1 = 0')
            )
            ->latest('id');
    }

    private function applySearch($query, string $find): void
    {
        $query->where(function ($subQuery) use ($find) {
            $subQuery
                ->where('id', 'like', '%' . $find . '%')
                ->orWhere('name', 'like', '%' . $find . '%')
                ->orWhere('email', 'like', '%' . $find . '%')
                ->orWhere('phone', 'like', '%' . $find . '%')
                ->orWhere('reference', 'like', '%' . $find . '%');
        });
    }

    private function refUserPayload(User $refUser): array
    {
        return [
            'id' => $refUser->id,
            'name' => $refUser->name,
            'email' => $refUser->email,
            'phone' => $refUser->phone,
            'comission' => 0,
            'join' => Carbon::parse($refUser->updated_at)->toFormattedDateString(),
        ];
    }
}
