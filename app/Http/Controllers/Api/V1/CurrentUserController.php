<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AuthenticatedRequest;
use App\Http\Resources\Api\V1\UserResource;

class CurrentUserController extends Controller
{
    public function __invoke(AuthenticatedRequest $request): UserResource
    {
        return new UserResource($request->authenticatedUser());
    }
}
