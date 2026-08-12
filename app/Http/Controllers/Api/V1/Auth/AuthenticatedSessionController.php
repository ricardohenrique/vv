<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Actions\Auth\AuthenticateUser;
use App\Actions\Auth\IssueDeviceToken;
use App\Actions\Auth\RevokeCurrentDeviceToken;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\AuthenticatedRequest;
use App\Http\Resources\Api\V1\AuthenticationResource;
use Illuminate\Http\Response;

class AuthenticatedSessionController extends Controller
{
    public function store(
        LoginRequest $request,
        AuthenticateUser $authenticateUser,
        IssueDeviceToken $issueDeviceToken,
    ): AuthenticationResource {
        $credentials = $request->credentials();
        $user = $authenticateUser($credentials['email'], $credentials['password']);

        return new AuthenticationResource(
            $user,
            $issueDeviceToken($user, $request->deviceName()),
        );
    }

    public function destroy(
        AuthenticatedRequest $request,
        RevokeCurrentDeviceToken $revokeCurrentDeviceToken,
    ): Response {
        $revokeCurrentDeviceToken($request->authenticatedUser());

        return response()->noContent();
    }
}
