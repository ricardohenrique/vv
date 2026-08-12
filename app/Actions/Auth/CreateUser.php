<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;

final class CreateUser
{
    /**
     * @param  array{name: string, email: string, password: string}  $attributes
     */
    public function __invoke(array $attributes): User
    {
        $user = User::query()->create($attributes);

        event(new Registered($user));

        return $user;
    }
}
