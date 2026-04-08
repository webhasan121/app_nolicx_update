<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
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
                'whatsapp_no' => $this->getEnvValue('WHATSAPP_NO'),
                'support_mail' => $this->getEnvValue('SUPPORT_MAIL'),
                'dbid_no' => $this->getEnvValue('DBID_NO'),
                'trade_license' => $this->getEnvValue('TRADE_LICENSE'),
                'playstore_link' => $this->getEnvValue('PLAYSTORE_LINK'),
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

        $this->setEnvValue('SUPPORT_MAIL', $validated['support_mail']);

        return redirect()->back()->with('success', 'Support email updated successfully!');

    }

    public function updateWhatsapp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'whatsapp_no' => 'required|string|min:11',
        ]);

        $this->setEnvValue('WHATSAPP_NO', $validated['whatsapp_no']);

        return redirect()->back()->with('success', 'WhatsApp number updated successfully!');
    }

    public function updateDBIDNo(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'dbid_no' => 'required|string',
        ]);

        $this->setEnvValue('DBID_NO', $validated['dbid_no']);

        return redirect()->back()->with('success', 'DBID no. updated successfully!');
    }

    public function updateTradeLicense(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'trade_license' => 'required|string',
        ]);

        $this->setEnvValue('TRADE_LICENSE', $validated['trade_license']);

        return redirect()->back()->with('success', 'Trade License no. updated successfully!');
    }

    public function updatePlaystoreLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'playstore_link' => 'required|string',
        ]);

        $this->setEnvValue('PLAYSTORE_LINK', $validated['playstore_link']);

        return redirect()->back()->with('success', 'Playstore link updated successfully!');
    }

    private function getEnvValue(string $key): string
    {
        $envPath = app()->environmentFilePath();

        if (!file_exists($envPath)) {
            return '';
        }

        $env = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($env as $line) {
            if (str_starts_with(trim($line), $key . '=')) {
                $value = explode('=', $line, 2)[1] ?? '';

                return trim($value, "\"'");
            }
        }

        return '';
    }

    private function setEnvValue(string $key, string $value): void
    {
        $envPath = app()->environmentFilePath();
        $env = file_get_contents($envPath);

        if (strpos($env, $key . '=') !== false) {
            $env = preg_replace('/' . preg_quote($key, '/') . '=.*/', $key . '=' . $value, $env);
        } else {
            $env .= "\n" . $key . '=' . $value;
        }

        file_put_contents($envPath, $env);
        Artisan::call('config:clear');
    }
}
