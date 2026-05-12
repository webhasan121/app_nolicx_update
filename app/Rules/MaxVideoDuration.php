<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class MaxVideoDuration implements ValidationRule
{
    public function __construct(private readonly int $seconds)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile) {
            return;
        }

        $duration = $this->durationInSeconds($value->getRealPath());

        if ($duration !== null && $duration > $this->seconds) {
            $fail("The {$attribute} must be {$this->seconds} seconds or less.");
        }
    }

    private function durationInSeconds(?string $path): ?float
    {
        if (!$path || !function_exists('exec')) {
            return null;
        }

        $command = 'ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 ' . escapeshellarg($path);
        $output = [];
        $exitCode = 1;

        @exec($command, $output, $exitCode);

        if ($exitCode !== 0 || empty($output[0]) || !is_numeric($output[0])) {
            return null;
        }

        return (float) $output[0];
    }
}
