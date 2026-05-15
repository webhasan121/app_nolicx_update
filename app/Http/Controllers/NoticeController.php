<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\NoticeRead;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class NoticeController extends Controller
{
    private const TARGET_ROLES = ['system', 'user', 'vendor', 'reseller', 'rider'];

    public function index()
    {
        $user = auth()->user();
        $canManage = $this->canManage($user);
        $query = $this->visibleNoticeQuery($user, $canManage);

        $notices = $query->paginate(config('app.paginate'))->withQueryString();

        return Inertia::render('Auth/Notices/Index', [
            'canManage' => $canManage,
            'targetRoles' => collect(self::TARGET_ROLES)->map(fn ($role) => [
                'value' => $role,
                'label' => $role === 'user' ? 'Users' : ucfirst($role),
            ])->values()->all(),
            'notices' => [
                'data' => $notices->getCollection()->map(fn (Notice $notice) => $this->serializeNotice($notice))->values()->all(),
                'links' => collect($notices->linkCollection())->map(fn ($link) => [
                    'url' => $link['url'],
                    'label' => strip_tags($link['label']),
                    'active' => $link['active'],
                ])->values()->all(),
                'from' => $notices->firstItem(),
                'to' => $notices->lastItem(),
                'total' => $notices->total(),
            ],
        ]);
    }

    public function feed(Request $request)
    {
        $user = $request->user();
        $canManage = $this->canManage($user);

        $query = $this->visibleNoticeQuery($user, $canManage);
        $unreadQuery = $this->unreadNoticeQuery((clone $query), $user->id);

        return response()->json([
            'latest_id' => (clone $query)->max('id') ?? 0,
            'today_count' => (clone $query)->whereDate('created_at', today())->count(),
            'unread_count' => (clone $unreadQuery)->count(),
            'notices' => (clone $query)
                ->limit(5)
                ->get()
                ->map(fn (Notice $notice) => $this->serializeNotice($notice))
                ->values()
                ->all(),
        ]);
    }

    public function markRead(Request $request, Notice $notice)
    {
        abort_unless($this->canViewNotice($notice, $request->user()), 403);

        NoticeRead::query()->firstOrCreate(
            [
                'notice_id' => $notice->id,
                'user_id' => $request->user()->id,
            ],
            [
                'read_at' => now(),
            ]
        );

        return response()->json(['ok' => true]);
    }

    public function markAllRead(Request $request)
    {
        $user = $request->user();
        $canManage = $this->canManage($user);
        $notices = $this->unreadNoticeQuery($this->visibleNoticeQuery($user, $canManage), $user->id)
            ->select('id')
            ->get();

        foreach ($notices as $notice) {
            NoticeRead::query()->firstOrCreate(
                [
                    'notice_id' => $notice->id,
                    'user_id' => $user->id,
                ],
                [
                    'read_at' => now(),
                ]
            );
        }

        return response()->json(['ok' => true]);
    }

    public function store(Request $request)
    {
        abort_unless($this->canManage(auth()->user()), 403);

        $validated = $this->validated($request);
        $validated['created_by'] = auth()->id();

        Notice::create($validated);

        return redirect()->back()->with('success', 'Notice created successfully.');
    }

    public function update(Request $request, Notice $notice)
    {
        abort_unless($this->canManage(auth()->user()), 403);

        $notice->update($this->validated($request));

        return redirect()->back()->with('success', 'Notice updated successfully.');
    }

    public function destroy(Notice $notice)
    {
        abort_unless($this->canManage(auth()->user()), 403);

        $notice->delete();

        return redirect()->back()->with('success', 'Notice deleted successfully.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'target_roles' => ['nullable', 'array'],
            'target_roles.*' => ['string', Rule::in(self::TARGET_ROLES)],
            'is_active' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:published_at'],
        ]);

        $targetRoles = array_values(array_unique(Arr::wrap($validated['target_roles'] ?? [])));
        $validated['target_roles'] = count($targetRoles) ? $targetRoles : null;
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $validated['published_at'] = $validated['published_at'] ?? null;
        $validated['expires_at'] = $validated['expires_at'] ?? null;

        return $validated;
    }

    private function serializeNotice(Notice $notice): array
    {
        return [
            'id' => $notice->id,
            'title' => $notice->title,
            'body' => $notice->body,
            'link_url' => $this->noticeLinkUrl($notice),
            'is_read' => $this->isReadByCurrentUser($notice),
            'target_roles' => $notice->target_roles ?? [],
            'is_active' => $notice->is_active,
            'published_at' => optional($notice->published_at)->format('Y-m-d\TH:i'),
            'published_at_formatted' => $notice->published_at?->toFormattedDateString() ?? 'Immediately',
            'expires_at' => optional($notice->expires_at)->format('Y-m-d\TH:i'),
            'expires_at_formatted' => $notice->expires_at?->toFormattedDateString() ?? 'No expiry',
            'created_at_formatted' => $notice->created_at?->toDayDateTimeString() ?? '',
            'creator_name' => $notice->creator?->name ?? 'System',
        ];
    }

    private function noticeLinkUrl(Notice $notice): ?string
    {
        $orderId = $notice->order_id ?? null;

        if (! $orderId) {
            preg_match('/order\s*#?(\d+)|#(\d+)/i', "{$notice->title} {$notice->body}", $matches);
            $orderId = (int) ($matches[1] ?? $matches[2] ?? 0);
        }

        if (! $orderId) {
            return null;
        }

        $user = auth()->user();
        $activeNav = $user?->active_nav;

        if ($user?->hasAnyRole(['system', 'admin']) || $activeNav === 'system') {
            return route('system.orders.details', ['id' => $orderId]);
        }

        if ($activeNav === 'vendor' || $user?->hasRole('vendor')) {
            return route('vendor.orders.view', ['order' => $orderId]);
        }

        if ($activeNav === 'reseller' || $user?->hasRole('reseller')) {
            return route('reseller.order.view', ['order' => $orderId]);
        }

        if ($activeNav === 'rider' || $user?->hasRole('rider')) {
            return route('rider.consignment.view', ['id' => $orderId]);
        }

        return route('user.orders.details', ['id' => $orderId]);
    }

    private function canManage($user): bool
    {
        return $user?->hasAnyRole(['system', 'admin']) ?? false;
    }

    private function visibleNoticeQuery($user, bool $canManage)
    {
        $query = Notice::query()
            ->with('creator:id,name')
            ->latest('published_at')
            ->latest();

        if (! $canManage) {
            $query->published()->forRoles($this->noticeRolesFor($user));
        }

        return $query;
    }

    private function noticeRolesFor($user): array
    {
        $roles = $user->getRoleNames()->values()->all();
        $activeNav = $user->active_nav;

        if (in_array($activeNav, self::TARGET_ROLES, true) && in_array($activeNav, $roles, true)) {
            return [$activeNav];
        }

        foreach (['user', 'vendor', 'reseller', 'rider'] as $role) {
            if (in_array($role, $roles, true)) {
                return [$role];
            }
        }

        return ['user'];
    }

    private function unreadNoticeQuery($query, int $userId)
    {
        return $query->whereDoesntHave('reads', function ($builder) use ($userId) {
            $builder->where('user_id', $userId);
        });
    }

    private function isReadByCurrentUser(Notice $notice): bool
    {
        $userId = auth()->id();

        return $userId
            ? $notice->reads()->where('user_id', $userId)->exists()
            : false;
    }

    private function canViewNotice(Notice $notice, $user): bool
    {
        $canManage = $this->canManage($user);

        return $this->visibleNoticeQuery($user, $canManage)
            ->whereKey($notice->id)
            ->exists();
    }
}
