<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserTask;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WalletTaskController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->queryFor($request);
        $tasks = $query->paginate(10)->withQueryString();

        return Inertia::render('User/Wallet/Task', [
            'tasks' => $this->mapTasks($tasks->items()),
            'pagination' => $this->paginationPayload($tasks),
            'filters' => [
                'find' => $request->string('find')->toString(),
            ],
            'printUrl' => route('user.wallet.tasks.print', [
                'find' => $request->string('find')->toString(),
            ]),
        ]);
    }

    public function print(Request $request)
    {
        $tasks = $this->queryFor($request)->get();

        return Inertia::render('User/Wallet/TaskPrint', [
            'tasks' => $this->mapTasks($tasks),
            'filters' => [
                'find' => $request->string('find')->toString(),
            ],
        ]);
    }

    private function queryFor(Request $request)
    {
        $query = UserTask::where(['user_id' => $request->user()->id])
            ->when($request->filled('find'), function ($query) use ($request) {
                $find = $request->string('find')->toString();

                $query->where(function ($query) use ($find) {
                    $query->where('id', 'like', "%{$find}%")
                        ->orWhere('coin', 'like', "%{$find}%")
                        ->orWhere('time', 'like', "%{$find}%")
                        ->orWhere('created_at', 'like', "%{$find}%");
                });
            })
            ->orderBy('id', 'desc');

        return $query;
    }

    private function mapTasks($tasks)
    {
        return collect($tasks)->map(function ($item) {
            $seconds = (int) ($item->time ?? 0);
            $m = floor($seconds / 60);
            $s = $seconds % 60;

            return [
                'id' => $item->id,
                'date' => Carbon::parse($item->created_at)->toFormattedDateString(),
                'earning' => $item->coin ?? 0,
                'time' => $m . ' : ' . $s . ' min',
            ];
        })->values();
    }

    private function paginationPayload($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'total' => $paginator->total(),
            'links' => collect($paginator->linkCollection())->map(function ($link) {
                return [
                    'url' => $link['url'],
                    'label' => strip_tags($link['label']),
                    'active' => $link['active'],
                ];
            })->values(),
        ];
    }
}
