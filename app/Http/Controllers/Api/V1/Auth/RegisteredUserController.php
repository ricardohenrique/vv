<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Actions\Auth\CreateUser;
use App\Actions\Auth\IssueDeviceToken;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Resources\Api\V1\AuthenticationResource;
use Illuminate\Http\JsonResponse;

class RegisteredUserController extends Controller
{
    public function store(
        RegisterRequest $request,
        CreateUser $createUser,
        IssueDeviceToken $issueDeviceToken,
    ): JsonResponse {
        $user = $createUser($request->userAttributes());

        return (new AuthenticationResource(
            $user,
            $issueDeviceToken($user, $request->deviceName()),
        ))->response()->setStatusCode(201);
    }
}
