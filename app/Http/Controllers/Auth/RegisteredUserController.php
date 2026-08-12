<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\CreateUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends Controller
{
    public function store(RegisterRequest $request, CreateUser $createUser): RedirectResponse
    {
        $user = $createUser($request->userAttributes());

        Auth::login($user);

        return redirect()->route('home');
    }
}
