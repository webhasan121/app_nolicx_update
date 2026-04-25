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
    public function indexReact(): Response
    {
        $columns = ['SL', 'Level Name', 'Requirements', 'Commission', 'Rerward', 'A/C'];
        $levels = Level::query()
            ->latest('id')
            ->get()
            ->values()
            ->map(function (Level $level, int $index) {
                return [
                    'sl' => $index + 1,
                    'id' => $level->id,
                    'name' => $level->name,
                    'req_users' => $level->req_users,
                    'vip_users' => $level->vip_users,
                    'bonus' => $level->bonus,
                    'rewards' => $level->rewards,
                    'status' => (bool) $level->status,
                ];
            })
            ->all();

        return Inertia::render('Auth/system/levels/Index', [
            'columns' => $columns,
            'levels' => $levels,
        ]);
    }

    public function historyReact(): Response
    {
        $columns = ['SL', 'Name of Users', 'From Level', 'To Level', 'Level-Up At'];
        $histories = LevelHistory::query()
            ->with([
                'user:id,name',
                'fromLevel:id,name',
                'toLevel:id,name',
            ])
            ->latest('id')
            ->get()
            ->values()
            ->map(function (LevelHistory $history, int $index) {
                return [
                    'sl' => $index + 1,
                    'id' => $history->id,
                    'user_name' => $history->user?->name,
                    'from_level_name' => $history->fromLevel?->name,
                    'to_level_name' => $history->toLevel?->name,
                    'created_at_formatted' => $history->created_at?->format('M d, Y'),
                ];
            })
            ->all();

        return Inertia::render('Auth/system/levels/History', [
            'columns' => $columns,
            'histories' => $histories,
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
}
