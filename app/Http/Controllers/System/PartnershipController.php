<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\DeveloperAccess;
use App\Models\ManagementAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PartnershipController extends Controller
{
    public function indexReact(): Response
    {
        return Inertia::render('Auth/system/partnership/Index');
    }

    public function developerReact(Request $request): Response
    {
        $find = trim((string) $request->query('find', ''));

        $applications = DeveloperAccess::query()
            ->with(['user', 'responder'])
            ->when($find !== '', function ($query) use ($find) {
                $query->where(function ($builder) use ($find) {
                    $builder
                        ->where('id', 'like', '%' . $find . '%')
                        ->orWhereHas('user', function ($userQuery) use ($find) {
                            $userQuery
                                ->where('name', 'like', '%' . $find . '%')
                                ->orWhere('email', 'like', '%' . $find . '%');
                        })
                        ->orWhereHas('responder', function ($userQuery) use ($find) {
                            $userQuery
                                ->where('name', 'like', '%' . $find . '%')
                                ->orWhere('email', 'like', '%' . $find . '%');
                        });
                });
            })
            ->latest('id')
            ->paginate(config('app.paginate'))
            ->withQueryString();

        return Inertia::render('Auth/system/partnership/Developer', [
            'applications' => [
                'data' => $applications->getCollection()->values()->map(function (DeveloperAccess $app, int $index) use ($applications) {
                    $status = $app->status === 1 ? 'Approved' : ($app->status === 0 ? 'Rejected' : 'Pending');
                    $reverseSerial = ($applications->total() - ($applications->firstItem() + $index - 1)) . '.';

                    return [
                        'id' => $app->id,
                        'sl' => $reverseSerial,
                        'user_name' => $app->user?->name,
                        'user_email' => $app->user?->email,
                        'status' => $app->status,
                        'status_text' => $status,
                        'responder_name' => $app->responder?->name ?? '-',
                    ];
                })->values()->all(),
                'links' => $applications->linkCollection()->map(fn ($link) => [
                    'url' => $link['url'],
                    'label' => $link['label'],
                    'active' => $link['active'],
                ])->values()->all(),
                'from' => $applications->firstItem(),
                'to' => $applications->lastItem(),
                'total' => $applications->total(),
            ],
            'filters' => [
                'find' => $find,
            ],
            'printUrl' => route('system.partnership.developer.print', [
                'find' => $find,
            ]),
        ]);
    }

    public function printDeveloperReact(Request $request): Response
    {
        $find = trim((string) $request->query('find', ''));

        $applications = DeveloperAccess::query()
            ->with(['user', 'responder'])
            ->when($find !== '', function ($query) use ($find) {
                $query->where(function ($builder) use ($find) {
                    $builder
                        ->where('id', 'like', '%' . $find . '%')
                        ->orWhereHas('user', function ($userQuery) use ($find) {
                            $userQuery
                                ->where('name', 'like', '%' . $find . '%')
                                ->orWhere('email', 'like', '%' . $find . '%');
                        })
                        ->orWhereHas('responder', function ($userQuery) use ($find) {
                            $userQuery
                                ->where('name', 'like', '%' . $find . '%')
                                ->orWhere('email', 'like', '%' . $find . '%');
                        });
                });
            })
            ->latest('id')
            ->get();

        return Inertia::render('Auth/system/partnership/DeveloperPrint', [
            'applications' => $applications->values()->map(function (DeveloperAccess $app, int $index) {
                return [
                    'id' => $app->id,
                    'sl' => $index + 1,
                    'user_name' => $app->user?->name,
                    'user_email' => $app->user?->email,
                    'status_text' => $app->status === 1 ? 'Approved' : ($app->status === 0 ? 'Rejected' : 'Pending'),
                    'responder_name' => $app->responder?->name ?? '-',
                ];
            })->all(),
            'filters' => [
                'find' => $find,
            ],
        ]);
    }

    public function acceptDeveloper(int $id): RedirectResponse
    {
        DeveloperAccess::where('id', $id)->update([
            'status' => 1,
            'response_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Application approved');
    }

    public function rejectDeveloper(int $id): RedirectResponse
    {
        DeveloperAccess::where('id', $id)->update([
            'status' => 0,
            'response_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Application rejected');
    }

    public function destroyDeveloper(int $id): RedirectResponse
    {
        DeveloperAccess::where('id', $id)->delete();

        return redirect()->back()->with('success', 'Application deleted');
    }

    public function managementReact(Request $request): Response
    {
        $find = trim((string) $request->query('find', ''));

        $applications = ManagementAccess::query()
            ->with(['user', 'responder'])
            ->when($find !== '', function ($query) use ($find) {
                $query->where(function ($builder) use ($find) {
                    $builder
                        ->where('id', 'like', '%' . $find . '%')
                        ->orWhereHas('user', function ($userQuery) use ($find) {
                            $userQuery
                                ->where('name', 'like', '%' . $find . '%')
                                ->orWhere('email', 'like', '%' . $find . '%');
                        })
                        ->orWhereHas('responder', function ($userQuery) use ($find) {
                            $userQuery
                                ->where('name', 'like', '%' . $find . '%')
                                ->orWhere('email', 'like', '%' . $find . '%');
                        });
                });
            })
            ->latest('id')
            ->paginate(config('app.paginate'))
            ->withQueryString();

        return Inertia::render('Auth/system/partnership/Management', [
            'applications' => [
                'data' => $applications->getCollection()->values()->map(function (ManagementAccess $app, int $index) use ($applications) {
                    $status = $app->status === 1 ? 'Approved' : ($app->status === 0 ? 'Rejected' : 'Pending');
                    $reverseSerial = ($applications->total() - ($applications->firstItem() + $index - 1)) . '.';

                    return [
                        'id' => $app->id,
                        'sl' => $reverseSerial,
                        'user_name' => $app->user?->name,
                        'user_email' => $app->user?->email,
                        'status' => $app->status,
                        'status_text' => $status,
                        'responder_name' => $app->responder?->name ?? '-',
                    ];
                })->values()->all(),
                'links' => $applications->linkCollection()->map(fn ($link) => [
                    'url' => $link['url'],
                    'label' => $link['label'],
                    'active' => $link['active'],
                ])->values()->all(),
                'from' => $applications->firstItem(),
                'to' => $applications->lastItem(),
                'total' => $applications->total(),
            ],
            'filters' => [
                'find' => $find,
            ],
            'printUrl' => route('system.partnership.management.print', [
                'find' => $find,
            ]),
        ]);
    }

    public function printManagementReact(Request $request): Response
    {
        $find = trim((string) $request->query('find', ''));

        $applications = ManagementAccess::query()
            ->with(['user', 'responder'])
            ->when($find !== '', function ($query) use ($find) {
                $query->where(function ($builder) use ($find) {
                    $builder
                        ->where('id', 'like', '%' . $find . '%')
                        ->orWhereHas('user', function ($userQuery) use ($find) {
                            $userQuery
                                ->where('name', 'like', '%' . $find . '%')
                                ->orWhere('email', 'like', '%' . $find . '%');
                        })
                        ->orWhereHas('responder', function ($userQuery) use ($find) {
                            $userQuery
                                ->where('name', 'like', '%' . $find . '%')
                                ->orWhere('email', 'like', '%' . $find . '%');
                        });
                });
            })
            ->latest('id')
            ->get();

        return Inertia::render('Auth/system/partnership/ManagementPrint', [
            'applications' => $applications->values()->map(function (ManagementAccess $app, int $index) {
                return [
                    'id' => $app->id,
                    'sl' => $index + 1,
                    'user_name' => $app->user?->name,
                    'user_email' => $app->user?->email,
                    'status_text' => $app->status === 1 ? 'Approved' : ($app->status === 0 ? 'Rejected' : 'Pending'),
                    'responder_name' => $app->responder?->name ?? '-',
                ];
            })->all(),
            'filters' => [
                'find' => $find,
            ],
        ]);
    }

    public function acceptManagement(int $id): RedirectResponse
    {
        ManagementAccess::where('id', $id)->update([
            'status' => 1,
            'response_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Application approved');
    }

    public function rejectManagement(int $id): RedirectResponse
    {
        ManagementAccess::where('id', $id)->update([
            'status' => 0,
            'response_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Application rejected');
    }

    public function destroyManagement(int $id): RedirectResponse
    {
        ManagementAccess::where('id', $id)->delete();

        return redirect()->back()->with('success', 'Application deleted');
    }
}
