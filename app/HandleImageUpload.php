<?php

namespace App;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandleImageUpload
{
    public function handleImageUpload($image, $directory = 'images', $oldImage = null)
    {
        if (!$image) return $oldImage;

        if ($oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs($directory, $filename, 'public');

        return $path;
    }
}
