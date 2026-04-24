<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Support\SystemSettings;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function indexReact(): Response
    {
        $queueStatus = Artisan::output();

        return Inertia::render('Auth/system/settings/index', [
            'settings' => [
                'isQueueRunning' => strpos($queueStatus, 'Queue worker is running') !== false,
                'whatsapp_no' => SystemSettings::get('WHATSAPP_NO'),
                'support_mail' => SystemSettings::get('SUPPORT_MAIL'),
                'dbid_no' => SystemSettings::get('DBID_NO'),
                'trade_license' => SystemSettings::get('TRADE_LICENSE'),
                'playstore_link' => SystemSettings::get('PLAYSTORE_LINK'),
                'developer_percentage' => SystemSettings::get('DEVELOPER_PERCENTAGE'),
                'management_percentage' => SystemSettings::get('MANAGEMENT_PERCENTAGE'),
            ],
        ]);
    }

    public function startQueue(): RedirectResponse
    {
        try {
            Artisan::call('queue:work');

            return redirect()->back()->with('success', 'Queue worker started successfully.');
        } catch (Exception $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function updateEmail(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'support_mail' => 'required|email|min:10',
        ]);

        SystemSettings::set('SUPPORT_MAIL', $validated['support_mail']);

        return redirect()->back()->with('success', 'Support email updated successfully!');

    }

    public function updateWhatsapp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'whatsapp_no' => 'required|string|min:11',
        ]);

        SystemSettings::set('WHATSAPP_NO', $validated['whatsapp_no']);

        return redirect()->back()->with('success', 'WhatsApp number updated successfully!');
    }

    public function updateDBIDNo(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'dbid_no' => 'required|string',
        ]);

        SystemSettings::set('DBID_NO', $validated['dbid_no']);

        return redirect()->back()->with('success', 'DBID no. updated successfully!');
    }

    public function updateTradeLicense(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'trade_license' => 'required|string',
        ]);

        SystemSettings::set('TRADE_LICENSE', $validated['trade_license']);

        return redirect()->back()->with('success', 'Trade License no. updated successfully!');
    }

    public function updatePlaystoreLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'playstore_link' => 'required|string',
        ]);

        SystemSettings::set('PLAYSTORE_LINK', $validated['playstore_link']);

        return redirect()->back()->with('success', 'Playstore link updated successfully!');
    }

    public function updateDeveloperPercentage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'developer_percentage' => 'required|numeric|min:0|max:100',
        ]);

        SystemSettings::set('DEVELOPER_PERCENTAGE', (string) $validated['developer_percentage']);

        return redirect()->back()->with('success', 'Developer percentage updated successfully!');
    }

    public function updateManagementPercentage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'management_percentage' => 'required|numeric|min:0|max:100',
        ]);

        SystemSettings::set('MANAGEMENT_PERCENTAGE', (string) $validated['management_percentage']);

        return redirect()->back()->with('success', 'Management percentage updated successfully!');
    }

}
