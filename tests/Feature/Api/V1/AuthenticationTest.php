<?php

use App\Actions\Auth\IssueDeviceToken;
use App\Models\User;

it('issues a scoped expiring device token for valid credentials', function () {
    $user = User::factory()->create();

    $response = $this->postJson(route('api.v1.auth.login'), [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'Test iPhone',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.token_type', 'Bearer')
        ->assertJsonPath('data.user.id', $user->id)
        ->assertJsonPath('data.user.email', $user->email)
        ->assertJsonStructure(['data' => ['token', 'expires_at']]);

    $token = $user->tokens()->sole();

    expect($token->name)->toBe('Test iPhone')
        ->and($token->can(IssueDeviceToken::ABILITY))->toBeTrue()
        ->and($token->expires_at)->not->toBeNull();
});

it('rejects invalid API credentials without creating a browser session', function () {
    $user = User::factory()->create();

    $this->postJson(route('api.v1.auth.login'), [
        'email' => $user->email,
        'password' => 'incorrect-password',
        'device_name' => 'Test iPhone',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors('email');

    $this->assertGuest();
    expect($user->tokens()->count())->toBe(0);
});

it('requires a bounded device name when issuing a token', function (array $payload) {
    $user = User::factory()->create();

    $this->postJson(route('api.v1.auth.login'), [
        'email' => $user->email,
        'password' => 'password',
        ...$payload,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors('device_name');
})->with([
    'missing' => [[]],
    'too long' => [['device_name' => str_repeat('a', 101)]],
]);

it('rate limits repeated API login failures', function () {
    $user = User::factory()->create();

    foreach (range(1, 5) as $attempt) {
        $this->postJson(route('api.v1.auth.login'), [
            'email' => $user->email,
            'password' => 'incorrect-password',
            'device_name' => 'Test iPhone',
        ])->assertUnprocessable();
    }

    $this->postJson(route('api.v1.auth.login'), [
        'email' => $user->email,
        'password' => 'incorrect-password',
        'device_name' => 'Test iPhone',
    ])->assertTooManyRequests();
});

it('registers through the API and returns a device token without a browser session', function () {
    $response = $this->postJson(route('api.v1.auth.register'), [
        'name' => 'Native User',
        'email' => 'native@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'device_name' => 'Test Android',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.user.name', 'Native User')
        ->assertJsonPath('data.user.email', 'native@example.com')
        ->assertJsonStructure(['data' => ['token', 'expires_at']]);

    $this->assertGuest();
    expect(User::query()->where('email', 'native@example.com')->sole()->tokens()->count())->toBe(1);
});

it('requires a bearer token and its API ability for protected endpoints', function () {
    $user = User::factory()->create();

    $this->getJson(route('api.v1.user.show'))->assertUnauthorized();

    $wrongAbilityToken = $user->createToken('Wrong ability', ['another:ability']);

    $this->withToken($wrongAbilityToken->plainTextToken)
        ->getJson(route('api.v1.user.show'))
        ->assertForbidden();
});

it('does not use the browser session as API authentication', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson(route('api.v1.user.show'))
        ->assertUnauthorized();
});

it('returns the current user for an authorized device token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('Test iPhone', [IssueDeviceToken::ABILITY], now()->addHour());

    $this->withToken($token->plainTextToken)
        ->getJson(route('api.v1.user.show'))
        ->assertOk()
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.email', $user->email);
});

it('revokes only the current device token on logout', function () {
    $user = User::factory()->create();
    $currentToken = $user->createToken('Current iPhone', [IssueDeviceToken::ABILITY], now()->addHour());
    $otherToken = $user->createToken('Other Android', [IssueDeviceToken::ABILITY], now()->addHour());

    $this->withToken($currentToken->plainTextToken)
        ->postJson(route('api.v1.auth.logout'))
        ->assertNoContent();

    $this->assertDatabaseMissing('personal_access_tokens', [
        'id' => $currentToken->accessToken->getKey(),
    ]);
    $this->assertModelExists($otherToken->accessToken);
});
