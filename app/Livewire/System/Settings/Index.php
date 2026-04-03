<?php

namespace App\Livewire\System\Settings;

use Exception;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Artisan;


#[layout('layouts.app')]
class Index extends Component {
    public $queueStatus;
    public $isQueueRunning;
    public $whatsapp_no;
    public $support_mail;
    public $dbid_no;
    public $trade_license;
    public $playstore_link;

    public function mount() {
        // Check if the queue worker is running
        $this->queueStatus = Artisan::output();
        // dd($this->queueStatus);
        $this->isQueueRunning = strpos($this->queueStatus, 'Queue worker is running') !== false;
        $this->whatsapp_no = $this->getEnvValue('WHATSAPP_NO');
        $this->support_mail = $this->getEnvValue('SUPPORT_MAIL');
        $this->dbid_no = $this->getEnvValue('DBID_NO');
        $this->trade_license = $this->getEnvValue('TRADE_LICENSE');
        $this->playstore_link = $this->getEnvValue('PLAYSTORE_LINK');
    }

    public function startQueue() {
        // This will start the queue worker
        try {

            Artisan::call('queue:work');
            $this->dispatch('success', 'Queue worker started successfully.');
        } catch (Exception $th) {
            $this->dispatch('error', $th->getMessage());
        }
    }

    public function render() {
        return view('livewire.system.settings.index');
    }

    /**
     * Safely read value from .env
     */
    private function getEnvValue($key) {
        $envPath = app()->environmentFilePath();

        if (!file_exists($envPath)) return '';

        $env = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($env as $line) {
            if (str_starts_with(trim($line), $key . '=')) {
                $value = explode('=', $line, 2)[1] ?? '';
                // Remove quotes if exist
                $value = trim($value, "\"'");
                return $value;
            }
        }

        return '';
    }

    public function updateEmail() {
        $this->validate([
            'support_mail' => 'required|email|min:10',
        ]);

        // Path to .env
        $envPath = app()->environmentFilePath();

        // Read .env contents
        $env = file_get_contents($envPath);

        // Replace or add SUPPORT_MAIL
        if (strpos($env, 'SUPPORT_MAIL=') !== false) {
            $env = preg_replace(
                '/SUPPORT_MAIL=.*/',
                'SUPPORT_MAIL=' . $this->support_mail,
                $env
            );
        } else {
            $env .= "\nSUPPORT_MAIL=" . $this->support_mail;
        }

        // Write back
        file_put_contents($envPath, $env);

        // Clear config cache so change takes effect
        Artisan::call('config:clear');

        $this->dispatch('success', 'Support email updated successfully!');
    }

    public function updateWhatsapp() {
        $this->validate([
            'whatsapp_no' => 'required|string|min:11',
        ]);

        // Path to .env
        $envPath = app()->environmentFilePath();

        // Read .env contents
        $env = file_get_contents($envPath);

        // Replace or add WHATSAPP_NO
        if (strpos($env, 'WHATSAPP_NO=') !== false) {
            $env = preg_replace(
                '/WHATSAPP_NO=.*/',
                'WHATSAPP_NO=' . $this->whatsapp_no,
                $env
            );
        } else {
            $env .= "\nWHATSAPP_NO=" . $this->whatsapp_no;
        }

        // Write back
        file_put_contents($envPath, $env);

        // Clear config cache so change takes effect
        Artisan::call('config:clear');

        $this->dispatch('success', 'WhatsApp number updated successfully!');
    }

    public function updateDBIDNo() {
        $this->validate([
            'dbid_no' => 'required|string',
        ]);

        // Path to .env
        $envPath = app()->environmentFilePath();

        // Read .env contents
        $env = file_get_contents($envPath);

        // Replace or add DBID_NO
        if (strpos($env, 'DBID_NO=') !== false) {
            $env = preg_replace(
                '/DBID_NO=.*/',
                'DBID_NO=' . $this->dbid_no,
                $env
            );
        } else {
            $env .= "\nDBID_NO=" . $this->dbid_no;
        }

        // Write back
        file_put_contents($envPath, $env);

        // Clear config cache so change takes effect
        Artisan::call('config:clear');

        $this->dispatch('success', 'DBID no. updated successfully!');
    }

    public function updateTradeLicense() {
        $this->validate([
            'trade_license' => 'required|string',
        ]);

        // Path to .env
        $envPath = app()->environmentFilePath();

        // Read .env contents
        $env = file_get_contents($envPath);

        // Replace or add TRADE_LICENSE
        if (strpos($env, 'TRADE_LICENSE=') !== false) {
            $env = preg_replace(
                '/TRADE_LICENSE=.*/',
                'TRADE_LICENSE=' . $this->trade_license,
                $env
            );
        } else {
            $env .= "\nTRADE_LICENSE=" . $this->trade_license;
        }

        // Write back
        file_put_contents($envPath, $env);

        // Clear config cache so change takes effect
        Artisan::call('config:clear');

        $this->dispatch('success', 'Trade License no. updated successfully!');
    }

    public function updatePlaystoreLink() {
        $this->validate([
            'playstore_link' => 'required|string',
        ]);

        // Path to .env
        $envPath = app()->environmentFilePath();

        // Read .env contents
        $env = file_get_contents($envPath);

        // Replace or add PLAYSTORE_LINK
        if (strpos($env, 'PLAYSTORE_LINK=') !== false) {
            $env = preg_replace(
                '/PLAYSTORE_LINK=.*/',
                'PLAYSTORE_LINK=' . $this->playstore_link,
                $env
            );
        } else {
            $env .= "\nPLAYSTORE_LINK=" . $this->playstore_link;
        }

        // Write back
        file_put_contents($envPath, $env);

        // Clear config cache so change takes effect
        Artisan::call('config:clear');

        $this->dispatch('success', 'Playstore link updated successfully!');
    }
}
