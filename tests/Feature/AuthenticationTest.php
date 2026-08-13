<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('opens the public home page and the administrator login page', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('articles/index'));
    $this->get(route('login'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('auth/login'));

    expect(route('login', absolute: false))->toBe('/admin');
    $this->get('/login')->assertNotFound();
});

it('requires authentication for the administrator area', function () {
    $this->get(route('admin.articles.index'))
        ->assertRedirect(route('login'));
});

it('redirects an authenticated administrator from the admin entry to article management', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->get(route('login'))
        ->assertRedirect(route('admin.articles.index'));
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

it('authenticates an administrator and opens article management', function () {
    $user = User::factory()->admin()->create();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('admin.articles.index'));

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

it('logs out an authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});
