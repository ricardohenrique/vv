<?php

namespace App\Http\Resources\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Sanctum\NewAccessToken;

class AuthenticationResource extends JsonResource
{
    public function __construct(
        private readonly User $user,
        private readonly NewAccessToken $token,
    ) {
        parent::__construct($user);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'token_type' => 'Bearer',
            'token' => $this->token->plainTextToken,
            'expires_at' => $this->token->accessToken->expires_at?->toISOString(),
            'user' => (new UserResource($this->user))->resolve($request),
        ];
    }
}
