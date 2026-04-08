<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\DeveloperAccess;
use App\Models\ManagementAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PartnershipController extends Controller
{
    public function developerReact(): Response
    {
        $applications = DeveloperAccess::with(['user', 'responder'])->latest('id')->paginate(20);

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
                'links' => $applications->linkCollection()->toArray(),
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

    public function managementReact(): Response
    {
        $applications = ManagementAccess::with(['user', 'responder'])->latest('id')->paginate(20);

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
                'links' => $applications->linkCollection()->toArray(),
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
