<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\LevelHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class LevelController extends Controller
{
    public function indexReact(Request $request): Response
    {
        $search = (string) $request->string('search');
        $columns = ['SL', 'Level Name', 'Requirements', 'Commission', 'Rerward', 'A/C'];
        $query = $this->levelQuery($search);
        $levels = $query->paginate(config('app.paginate'))->withQueryString();

        return Inertia::render('Auth/system/levels/Index', [
            'columns' => $columns,
            'filters' => [
                'search' => $search,
            ],
            'levels' => $this->paginatedLevels($levels),
            'printUrl' => route('system.levels.print', [
                'search' => $search,
            ]),
        ]);
    }

    public function historyReact(Request $request): Response
    {
        $search = (string) $request->string('search');
        $columns = ['SL', 'Name of Users', 'From Level', 'To Level', 'Level-Up At'];
        $query = $this->historyQuery($search);
        $histories = $query->paginate(config('app.paginate'))->withQueryString();

        return Inertia::render('Auth/system/levels/History', [
            'columns' => $columns,
            'filters' => [
                'search' => $search,
            ],
            'histories' => $this->paginatedHistories($histories),
            'printUrl' => route('system.levels.history.print', [
                'search' => $search,
            ]),
        ]);
    }

    public function printReact(Request $request): Response
    {
        $search = (string) $request->string('search');

        return Inertia::render('Auth/system/levels/Print', [
            'search' => $search,
            'levels' => $this->levelQuery($search)
                ->get()
                ->values()
                ->map(fn (Level $level, int $index) => $this->levelPayload($level, $index + 1))
                ->all(),
        ]);
    }

    public function printHistoryReact(Request $request): Response
    {
        $search = (string) $request->string('search');

        return Inertia::render('Auth/system/levels/HistoryPrint', [
            'search' => $search,
            'histories' => $this->historyQuery($search)
                ->get()
                ->values()
                ->map(fn (LevelHistory $history, int $index) => $this->historyPayload($history, $index + 1))
                ->all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request);

        Level::create([
            'name' => $validated['name'],
            'req_users' => $validated['req_users'],
            'vip_users' => $validated['vip_users'],
            'bonus' => $validated['bonus'],
            'rewards' => $validated['rewards'],
            'status' => true,
        ]);

        return redirect()
            ->route('system.levels.index')
            ->with('success', 'Level added successfully');
    }

    public function update(Request $request, Level $level): RedirectResponse
    {
        $validated = $this->validatePayload($request, $level);

        $level->update([
            'name' => $validated['name'],
            'req_users' => $validated['req_users'],
            'vip_users' => $validated['vip_users'],
            'bonus' => $validated['bonus'],
            'rewards' => $validated['rewards'],
        ]);

        return redirect()
            ->route('system.levels.index')
            ->with('success', 'Level updated successfully.');
    }

    public function destroy(Level $level): RedirectResponse
    {
        $level->delete();

        return redirect()
            ->route('system.levels.index')
            ->with('success', 'Level deleted successfully.');
    }

    private function validatePayload(Request $request, ?Level $level = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'req_users' => ['required', 'integer', 'min:0'],
            'vip_users' => ['required', 'integer', 'min:0'],
            'bonus' => ['nullable', 'decimal:2,2', 'min:0'],
            'rewards' => ['required', 'string', 'max:500'],
        ]);

        $slug = Str::slug($validated['name']);

        $slugExists = Level::query()
            ->where('slug', $slug)
            ->when($level, function ($query) use ($level) {
                $query->where('id', '!=', $level->id);
            })
            ->exists();

        if ($slugExists) {
            throw ValidationException::withMessages([
                'name' => 'The name has already been taken.',
            ]);
        }

        return $validated;
    }

    private function levelQuery(string $search)
    {
        return Level::query()
            ->latest('id')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('req_users', 'like', '%' . $search . '%')
                        ->orWhere('vip_users', 'like', '%' . $search . '%')
                        ->orWhere('bonus', 'like', '%' . $search . '%')
                        ->orWhere('rewards', 'like', '%' . $search . '%');
                });
            });
    }

    private function historyQuery(string $search)
    {
        return LevelHistory::query()
            ->with([
                'user:id,name',
                'fromLevel:id,name',
                'toLevel:id,name',
            ])
            ->latest('id')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder
                        ->whereHas('user', fn ($q) => $q->where('name', 'like', '%' . $search . '%'))
                        ->orWhereHas('fromLevel', fn ($q) => $q->where('name', 'like', '%' . $search . '%'))
                        ->orWhereHas('toLevel', fn ($q) => $q->where('name', 'like', '%' . $search . '%'));
                });
            });
    }

    private function paginatedLevels($levels): array
    {
        return [
            'data' => $levels->getCollection()
                ->values()
                ->map(fn (Level $level, int $index) => $this->levelPayload(
                    $level,
                    (($levels->currentPage() - 1) * $levels->perPage()) + $index + 1
                ))
                ->all(),
            'links' => $this->paginationLinks($levels),
            'from' => $levels->firstItem(),
            'to' => $levels->lastItem(),
            'total' => $levels->total(),
        ];
    }

    private function paginatedHistories($histories): array
    {
        return [
            'data' => $histories->getCollection()
                ->values()
                ->map(fn (LevelHistory $history, int $index) => $this->historyPayload(
                    $history,
                    (($histories->currentPage() - 1) * $histories->perPage()) + $index + 1
                ))
                ->all(),
            'links' => $this->paginationLinks($histories),
            'from' => $histories->firstItem(),
            'to' => $histories->lastItem(),
            'total' => $histories->total(),
        ];
    }

    private function paginationLinks($paginator): array
    {
        return collect($paginator->linkCollection())->map(function ($link) {
            return [
                'url' => $link['url'],
                'label' => strip_tags($link['label']),
                'active' => $link['active'],
            ];
        })->values()->all();
    }

    private function levelPayload(Level $level, int $sl): array
    {
        return [
            'sl' => $sl,
            'id' => $level->id,
            'name' => $level->name,
            'req_users' => $level->req_users,
            'vip_users' => $level->vip_users,
            'bonus' => $level->bonus,
            'rewards' => $level->rewards,
            'status' => (bool) $level->status,
        ];
    }

    private function historyPayload(LevelHistory $history, int $sl): array
    {
        return [
            'sl' => $sl,
            'id' => $history->id,
            'user_name' => $history->user?->name,
            'from_level_name' => $history->fromLevel?->name,
            'to_level_name' => $history->toLevel?->name,
            'created_at_formatted' => $history->created_at?->format('M d, Y'),
        ];
    }
}
