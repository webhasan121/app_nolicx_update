<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DistributeComissions;
use App\Models\ResellerResellProfits;
use App\Models\TakeComissions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WalletComissionController extends Controller
{
    public function index(Request $request)
    {
        $nav = $request->query('nav', 'earn');
        $set = $request->query('set', 'com');
        $find = trim((string) $request->query('find', ''));

        $query = $this->queryFor($request, $nav, $set);
        $this->applySearch($query, $nav, $set, $find);

        $data = $query
            ->latest('id')
            ->paginate(config('app.paginate'))
            ->withQueryString();

        return Inertia::render('User/Wallet/Comission', [
            'nav' => $nav,
            'set' => $set,
            'filters' => [
                'find' => $find,
            ],
            'rows' => $this->mapRows(collect($data->items()), $nav, $set),
            'pagination' => $this->paginationPayload($data),
            'printUrl' => route('user.wallet.earn-comissions.print', [
                'nav' => $nav,
                'set' => $set,
                'find' => $find,
            ]),
        ]);
    }

    public function print(Request $request)
    {
        $nav = $request->query('nav', 'earn');
        $set = $request->query('set', 'com');
        $find = trim((string) $request->query('find', ''));

        $query = $this->queryFor($request, $nav, $set);
        $this->applySearch($query, $nav, $set, $find);

        return Inertia::render('User/Wallet/ComissionPrint', [
            'nav' => $nav,
            'set' => $set,
            'filters' => [
                'find' => $find,
            ],
            'rows' => $this->mapRows($query->latest('id')->get(), $nav, $set),
        ]);
    }

    private function queryFor(Request $request, string $nav, string $set)
    {
        if ($nav === 'system') {
            return TakeComissions::with('product')
                ->where(['user_id' => $request->user()->id, 'confirmed' => true]);
        }

        if ($set === 'prof') {
            return ResellerResellProfits::with('product')
                ->where(['to' => $request->user()->id, 'confirmed' => true]);
        }

        return DistributeComissions::with('product')
            ->where(['user_id' => $request->user()->id, 'confirmed' => true]);
    }

    private function applySearch($query, string $nav, string $set, string $find): void
    {
        if ($find === '') {
            return;
        }

        $query->where(function ($subQuery) use ($nav, $set, $find) {
            $subQuery
                ->where('id', 'like', '%' . $find . '%')
                ->orWhereHas('product', function ($productQuery) use ($find) {
                    $productQuery
                        ->where('name', 'like', '%' . $find . '%')
                        ->orWhere('slug', 'like', '%' . $find . '%');
                });

            if ($set === 'prof') {
                $subQuery->orWhere('profit', 'like', '%' . $find . '%');
                return;
            }

            if ($nav === 'system') {
                $subQuery
                    ->orWhere('take_comission', 'like', '%' . $find . '%')
                    ->orWhere('order_id', 'like', '%' . $find . '%');
                return;
            }

            $subQuery
                ->orWhere('amount', 'like', '%' . $find . '%')
                ->orWhere('info', 'like', '%' . $find . '%')
                ->orWhere('order_id', 'like', '%' . $find . '%');
        });
    }

    private function mapRows($items, string $nav, string $set)
    {
        return collect($items)->map(function ($item) use ($nav, $set) {
            if ($set === 'prof') {
                return [
                    'id' => $item->id,
                    'product' => $item->product?->name ?? 0,
                    'profit' => $item->profit ?? 0,
                    'date' => Carbon::parse($item->updated_at)->toFormattedDateString(),
                ];
            }

            if ($nav === 'system') {
                return [
                    'id' => $item->id,
                    'amount' => $item->take_comission,
                    'product' => $item->product?->name ?? 'N/A',
                    'order' => $item->order_id ?? 'N/A',
                    'date' => Carbon::parse($item->updated_at)->toFormattedDateString(),
                ];
            }

            return [
                'id' => $item->id,
                'product' => $this->earnCommissionSource($item),
                'purpose' => $item->info ?? 'N/A',
                'order' => $item->order_id ?? 'N/A',
                'amount' => $item->amount ?? 0,
                'date' => Carbon::parse($item->updated_at)->toFormattedDateString(),
            ];
        })->values();
    }

    private function earnCommissionSource(DistributeComissions $item): string
    {
        if ($item->product?->name) {
            return $item->product->name;
        }

        $purpose = trim((string) ($item->info ?? ''));
        $orderId = $item->order_id ? 'Order #' . $item->order_id : '';

        return trim(collect([$purpose, $orderId])->filter()->implode(' - ')) ?: 'N/A';
    }

    private function paginationPayload($data): array
    {
        return [
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'per_page' => $data->perPage(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
            'total' => $data->total(),
            'links' => collect($data->linkCollection())->map(function ($link) {
                return [
                    'url' => $link['url'],
                    'label' => strip_tags($link['label']),
                    'active' => $link['active'],
                ];
            })->values()->all(),
        ];
    }
}
