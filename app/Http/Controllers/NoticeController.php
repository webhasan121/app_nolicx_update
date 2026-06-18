<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\NoticeRead;
use App\Models\cod;
use App\Models\syncOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class NoticeController extends Controller
{
    private const TARGET_ROLES = ['system', 'user', 'vendor', 'reseller', 'rider'];

    public function index(Request $request)
    {
        $user = auth()->user();
        $canManage = $this->canManage($user);
        $requestedRoles = $this->requestedNoticeRoles($request, $user);
        $query = $this->visibleNoticeQuery($user, $canManage, $requestedRoles);
        $filters = $this->noticeFilters($request);

        if ($this->shouldUsePersonalUserScope($request, $requestedRoles)) {
            $this->applyUserNoticeScope($query, $user);
        }

        $this->applyNoticeFilters($query, $filters);

        $notices = $query->paginate(50)->withQueryString();
        $linkRole = $requestedRoles[0] ?? null;
        $serializedNotices = $this->serializeNoticePage($notices, $linkRole);

        if ($request->expectsJson()) {
            return response()->json([
                'notices' => $serializedNotices,
            ]);
        }

        return Inertia::render('Auth/Notices/Index', [
            'canManage' => $canManage,
            'noticeRole' => $linkRole,
            'personal' => $request->boolean('personal'),
            'targetRoles' => collect(self::TARGET_ROLES)->map(fn ($role) => [
                'value' => $role,
                'label' => $role === 'user' ? 'Users' : ucfirst($role),
            ])->values()->all(),
            'filters' => $filters,
            'notices' => $serializedNotices,
        ]);
    }

    public function feed(Request $request)
    {
        $user = $request->user();
        $canManage = $this->canManage($user);

        $requestedRoles = $this->requestedNoticeRoles($request, $user);
        $linkRole = $requestedRoles[0] ?? null;
        $query = $this->visibleNoticeQuery($user, $canManage, $requestedRoles);

        if ($this->shouldUsePersonalUserScope($request, $requestedRoles)) {
            $this->applyUserNoticeScope($query, $user);
        }

        $unreadQuery = $this->unreadNoticeQuery((clone $query), $user->id);

        return response()->json([
            'latest_id' => (clone $query)->max('id') ?? 0,
            'today_count' => (clone $query)->whereDate('created_at', today())->count(),
            'unread_count' => (clone $unreadQuery)->count(),
            'notices' => (clone $query)
                ->limit(5)
                ->get()
                ->map(fn (Notice $notice) => $this->serializeNotice($notice, $linkRole))
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
        $requestedRoles = $this->requestedNoticeRoles($request, $user);
        $query = $this->visibleNoticeQuery($user, $canManage, $requestedRoles);

        if ($this->shouldUsePersonalUserScope($request, $requestedRoles)) {
            $this->applyUserNoticeScope($query, $user);
        }

        $notices = $this->unreadNoticeQuery($query, $user->id)
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

    public function bulkDestroy(Request $request)
    {
        abort_unless($this->canManage(auth()->user()), 403);

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:50'],
            'ids.*' => ['integer', 'distinct', 'exists:notices,id'],
        ]);

        $deleted = Notice::query()
            ->whereIn('id', $validated['ids'])
            ->delete();

        return response()->json([
            'deleted' => $deleted,
            'ids' => $validated['ids'],
        ]);
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
        $validated['published_at'] = ! empty($validated['published_at'])
            ? Carbon::parse($validated['published_at'])->startOfDay()
            : null;
        $validated['expires_at'] = ! empty($validated['expires_at'])
            ? Carbon::parse($validated['expires_at'])->endOfDay()
            : null;

        return $validated;
    }

    private function serializeNotice(Notice $notice, ?string $linkRole = null): array
    {
        return [
            'id' => $notice->id,
            'title' => $notice->title,
            'body' => $notice->body,
            'target_user_id' => $notice->target_user_id,
            'link_url' => $this->noticeLinkUrl($notice, $linkRole),
            'is_read' => $this->isReadByCurrentUser($notice),
            'target_roles' => $notice->target_roles ?? [],
            'is_active' => $notice->is_active,
            'published_at' => optional($notice->published_at)->format('Y-m-d'),
            'published_at_formatted' => $notice->published_at?->toFormattedDateString() ?? 'Immediately',
            'expires_at' => optional($notice->expires_at)->format('Y-m-d'),
            'expires_at_formatted' => $notice->expires_at?->toFormattedDateString() ?? 'No expiry',
            'created_at_formatted' => $notice->created_at?->timezone(config('app.timezone'))->format('D, M j, Y g:i A') ?? '',
            'creator_name' => $notice->creator?->name ?? 'System',
        ];
    }

    private function serializeNoticePage($notices, ?string $linkRole = null): array
    {
        return [
            'data' => $notices->getCollection()->map(fn (Notice $notice) => $this->serializeNotice($notice, $linkRole))->values()->all(),
            'from' => $notices->firstItem(),
            'to' => $notices->lastItem(),
            'total' => $notices->total(),
            'current_page' => $notices->currentPage(),
            'last_page' => $notices->lastPage(),
            'per_page' => $notices->perPage(),
            'has_more' => $notices->hasMorePages(),
        ];
    }

    private function noticeFilters(Request $request): array
    {
        return [
            'search' => trim((string) $request->query('search', '')),
            'start_date' => $request->query('start_date', ''),
            'end_date' => $request->query('end_date', ''),
        ];
    }

    private function applyNoticeFilters($query, array $filters): void
    {
        $search = $filters['search'] ?? '';

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('title', 'like', '%' . $search . '%')
                    ->orWhere('body', 'like', '%' . $search . '%')
                    ->orWhere('order_id', 'like', '%' . $search . '%')
                    ->orWhereHas('creator', function ($creatorQuery) use ($search) {
                        $creatorQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $startDate = $filters['start_date'] ?? '';
        $endDate = $filters['end_date'] ?? '';

        if ($startDate !== '' || $endDate !== '') {
            $start = $this->parseNoticeDate($startDate)?->startOfDay();
            $end = $this->parseNoticeDate($endDate)?->endOfDay();

            if (! $start && ! $end) {
                return;
            }

            if ($start && $end && $start->gt($end)) {
                [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
            }

            $query->where(function ($dateQuery) use ($start, $end) {
                $this->applyNoticeDateRange($dateQuery, 'published_at', $start, $end);
                $dateQuery->orWhere(function ($fallbackQuery) use ($start, $end) {
                    $fallbackQuery->whereNull('published_at');
                    $this->applyNoticeDateRange($fallbackQuery, 'created_at', $start, $end);
                });
            });
        }
    }

    private function applyNoticeDateRange($query, string $column, ?Carbon $start, ?Carbon $end): void
    {
        if ($start && $end) {
            $query->whereBetween($column, [$start, $end]);

            return;
        }

        if ($start) {
            $query->where($column, '>=', $start);

            return;
        }

        if ($end) {
            $query->where($column, '<=', $end);
        }
    }

    private function parseNoticeDate(?string $value): ?Carbon
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function noticeLinkUrl(Notice $notice, ?string $linkRole = null): ?string
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

        if ($linkRole === 'user') {
            return route('user.orders.details', ['id' => $orderId]);
        }

        if ($linkRole === 'vendor') {
            $order = \App\Models\Order::query()->find($orderId);

            if ($order && (int) $order->belongs_to === (int) $user?->id && $order->belongs_to_type === 'vendor') {
                return route('vendor.orders.view', ['order' => $orderId]);
            }

            if ($order && $this->orderHasVendorReselProduct($order, (int) $user?->id)) {
                return route('vendor.products.view');
            }

            return null;
        }

        if ($linkRole === 'reseller') {
            return route('reseller.order.view', ['order' => $orderId]);
        }

        if ($linkRole === 'rider') {
            return $this->riderConsignmentUrl($orderId);
        }

        if ($linkRole === 'system') {
            return route('system.orders.details', ['id' => $orderId]);
        }

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
            return $this->riderConsignmentUrl($orderId);
        }

        return route('user.orders.details', ['id' => $orderId]);
    }

    private function riderConsignmentUrl(int $orderId): string
    {
        $riderId = auth()->id();
        $orderIds = $this->riderOrderIds($orderId);
        $query = cod::query()
            ->whereIn('order_id', $orderIds);

        if ($riderId) {
            $query->where('rider_id', $riderId);
        }

        $consignmentId = $query
            ->latest('id')
            ->value('id');

        return $consignmentId
            ? route('rider.consignment.view', ['id' => $consignmentId])
            : route('rider.consignment');
    }

    private function riderOrderIds(int $orderId): array
    {
        $syncedOrderId = syncOrder::query()
            ->where('user_order_id', $orderId)
            ->value('reseller_order_id');

        return collect([$orderId, $syncedOrderId])
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function canManage($user): bool
    {
        return $user?->hasAnyRole(['system', 'admin']) ?? false;
    }

    private function visibleNoticeQuery($user, bool $canManage, ?array $roles = null)
    {
        $query = Notice::query()
            ->with('creator:id,name')
            ->latest('published_at')
            ->latest();

        $effectiveRoles = $roles;

        if ($effectiveRoles) {
            $query->published()->forRoles($effectiveRoles);
        } elseif (! $canManage) {
            $effectiveRoles = $this->noticeRolesFor($user);
            $query->published()->forRoles($effectiveRoles);
        }

        if (! $canManage && $effectiveRoles === ['user']) {
            $this->applyUserNoticeScope($query, $user);
        }

        if (! $canManage && $effectiveRoles === ['rider']) {
            $query->where(function ($noticeQuery) use ($user) {
                $noticeQuery
                    ->whereNull('order_id')
                    ->orWhereHas('order.hasRider', fn ($consignmentQuery) => $consignmentQuery->where('rider_id', $user->id));
            });
        }

        if (! $canManage && in_array($effectiveRoles, [['vendor'], ['reseller']], true)) {
            $this->applySellerNoticeScope($query, $user, $effectiveRoles[0]);
        }

        return $query;
    }

    private function requestedNoticeRoles(Request $request, $user): ?array
    {
        $role = $request->query('role');

        if (! in_array($role, self::TARGET_ROLES, true)) {
            return null;
        }

        $roles = $user->getRoleNames()->values()->all();

        if ($role === 'user' || in_array($role, $roles, true)) {
            return [$role];
        }

        return null;
    }

    private function shouldUsePersonalUserScope(Request $request, ?array $roles): bool
    {
        return $roles === ['user'] && $request->boolean('personal');
    }

    private function applyUserNoticeScope($query, $user): void
    {
        $query->where(function ($builder) use ($user) {
            $builder
                ->where(function ($targetQuery) use ($user) {
                    $targetQuery
                        ->where('target_user_id', $user->id)
                        ->orWhereNull('target_user_id');
                })
                ->where(function ($orderQuery) use ($user) {
                    $orderQuery
                        ->whereNull('order_id')
                        ->orWhereHas('order', fn ($order) => $order->where('user_id', $user->id));
                });
        });
    }

    private function applySellerNoticeScope($query, $user, string $role): void
    {
        if ($role === 'vendor') {
            $query->where(function ($noticeQuery) use ($user) {
                $noticeQuery
                    ->whereNull('order_id')
                    ->orWhereHas('order', function ($orderQuery) use ($user) {
                        $orderQuery->where(function ($sellerQuery) use ($user) {
                            $sellerQuery
                                ->where(function ($directQuery) use ($user) {
                                    $directQuery
                                        ->where('belongs_to', $user->id)
                                        ->where('belongs_to_type', 'vendor');
                                })
                                ->orWhereHas('cartOrders.product.isResel', function ($reselQuery) use ($user) {
                                    $reselQuery->where('belongs_to', $user->id);
                                });
                        });
                    });
            });

            return;
        }

        $query->where(function ($noticeQuery) use ($user, $role) {
            $noticeQuery
                ->whereNull('order_id')
                ->orWhereHas('order', function ($orderQuery) use ($user, $role) {
                    $orderQuery
                        ->where('belongs_to', $user->id)
                        ->where('belongs_to_type', $role);
                });
        });
    }

    private function orderHasVendorReselProduct($order, int $vendorId): bool
    {
        if (! $vendorId) {
            return false;
        }

        return $order
            ->cartOrders()
            ->whereHas('product.isResel', fn ($reselQuery) => $reselQuery->where('belongs_to', $vendorId))
            ->exists();
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
        $roles = $canManage ? null : $this->noticeRolesFor($user);

        return $this->visibleNoticeQuery($user, $canManage, $roles)
            ->whereKey($notice->id)
            ->exists();
    }

}
