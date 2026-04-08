<?php

namespace App;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandleImageUpload
{
    public function handleImageUpload($image, $directory = 'images', $oldImage = null)
    {
        if (!$image) return $oldImage;

        if ($oldImage) {
            Storage::disk('public')->delete($oldImage);
            $publicOldImage = public_path('storage/' . ltrim($oldImage, '/'));
            if (File::exists($publicOldImage)) {
                File::delete($publicOldImage);
            }
        }

        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs($directory, $filename, 'public');
        $storedFile = storage_path('app/public/' . $path);
        $publicFile = public_path('storage/' . $path);
        $publicDirectory = dirname($publicFile);

        if (!File::isDirectory($publicDirectory)) {
            File::makeDirectory($publicDirectory, 0755, true);
        }

        if (File::exists($storedFile)) {
            File::copy($storedFile, $publicFile);
        }

        return $path;
    }
}
