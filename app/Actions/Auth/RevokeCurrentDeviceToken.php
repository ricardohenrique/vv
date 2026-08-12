<?php

namespace App\Actions\Auth;

use App\Models\User;

final class RevokeCurrentDeviceToken
{
    public function __invoke(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
