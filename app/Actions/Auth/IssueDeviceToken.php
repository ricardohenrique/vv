<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Config;
use Laravel\Sanctum\NewAccessToken;

final class IssueDeviceToken
{
    public const string ABILITY = 'api:access';

    public function __invoke(User $user, string $deviceName): NewAccessToken
    {
        return $user->createToken(
            $deviceName,
            [self::ABILITY],
            now()->addMinutes(Config::integer('sanctum.expiration')),
        );
    }
}
