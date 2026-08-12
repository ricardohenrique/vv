<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('opens the login page from the web root', function () {
    $this->get('/')
        ->assertRedirect(route('login'));

    $this->get(route('login'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('auth/login'));
});

it('requires authentication for the boilerplate home page', function () {
    $this->get(route('home'))
        ->assertRedirect(route('login'));
});

it('serves the native web entry without redirecting guests', function () {
    $this->get(route('mobile.web.entry'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('auth/login'));
});

it('serves the authenticated experience from the native web entry', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('mobile.web.entry'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('welcome'));
});

it('opens the registration page', function () {
    $this->get(route('register'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('auth/register'));
});

it('redirects authenticated users away from registration', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('register'))
        ->assertRedirect(route('home'));
});

it('registers and authenticates a new user', function () {
    $this->post(route('register.store'), [
        'name' => 'New User',
        'email' => 'new@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('home'));

    $user = User::query()->where('email', 'new@example.com')->firstOrFail();

    $this->assertAuthenticatedAs($user);
    $this->assertDatabaseHas('users', [
        'name' => 'New User',
        'email' => 'new@example.com',
    ]);
});

it('validates new user registration details', function () {
    $existingUser = User::factory()->create();

    $this->from(route('register'))->post(route('register.store'), [
        'name' => '',
        'email' => $existingUser->email,
        'password' => 'password',
        'password_confirmation' => 'different-password',
    ])->assertRedirect(route('register'))
        ->assertSessionHasErrors(['name', 'email', 'password']);

    $this->assertGuest();
});

it('authenticates a user and opens the boilerplate', function () {
    $user = User::factory()->create();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('home'));

    $this->assertAuthenticatedAs($user);
});

it('rejects invalid credentials', function () {
    $user = User::factory()->create();

    $this->from(route('login'))->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'incorrect-password',
    ])->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('rate limits repeated login failures', function () {
    $user = User::factory()->create();

    foreach (range(1, 5) as $attempt) {
        $this->from(route('login'))->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'incorrect-password',
        ])->assertSessionHasErrors('email');
    }

    $response = $this->from(route('login'))->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'incorrect-password',
    ]);

    $response->assertSessionHasErrors('email');

    expect($response->getSession()->get('errors')->first('email'))
        ->not->toBe(trans('auth.failed'));
});

it('rate limits repeated registration attempts', function () {
    foreach (range(1, 5) as $attempt) {
        $this->post(route('register.store'), [])->assertSessionHasErrors(['name', 'email', 'password']);
    }

    $this->post(route('register.store'), [])->assertTooManyRequests();
});

it('logs out an authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});
