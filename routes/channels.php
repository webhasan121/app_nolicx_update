<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('notices.{role}', function ($user, string $role) {
    if ($role === 'user') {
        return true;
    }

    if ($role === 'system') {
        return $user->hasAnyRole(['system', 'admin']);
    }

    return in_array($role, ['vendor', 'reseller', 'rider'], true)
        && $user->hasRole($role);
});

Broadcast::channel('notices.user.{targetUserId}', function ($user, int $targetUserId) {
    return (int) $user->id === $targetUserId || $user->hasAnyRole(['system', 'admin']);
});
