<?php

use App\Models\Article;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

it('requires an administrator to access article management', function () {
    $this->get(route('admin.articles.index'))->assertRedirect(route('login'));

    $this->actingAs(User::factory()->create())
        ->get(route('admin.articles.index'))
        ->assertForbidden();
});

it('allows an administrator to create and edit a draft', function () {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post(route('admin.articles.store'), [
        'title' => 'Excellent headphones',
        'slug' => 'excellent-headphones',
        'image' => UploadedFile::fake()->image('headphones.jpg'),
        'image_alt' => 'Black headphones on a desk',
        'summary' => 'A concise and balanced headphone review.',
        'body' => 'The complete review body with all of the useful details.',
        'rating' => '8.7',
        'category' => 'Audio',
        'tags' => 'Headphones, Wireless',
        'affiliate_url' => 'https://example.com/product',
    ])->assertRedirect();

    $article = Article::query()->where('slug', 'excellent-headphones')->firstOrFail();
    expect($article->published_at)->toBeNull()
        ->and($article->author->is($admin))->toBeTrue()
        ->and($article->tags)->toHaveCount(2);
    Storage::disk('public')->assertExists($article->image_path);

    $this->actingAs($admin)->put(route('admin.articles.update', $article), [
        'title' => 'Updated headphones review',
        'slug' => 'excellent-headphones',
        'image_alt' => 'Black headphones on a desk',
        'summary' => 'An updated and balanced headphone review.',
        'body' => 'The updated complete review body.',
        'rating' => '9.0',
        'category' => 'Audio',
        'tags' => 'Headphones',
        'affiliate_url' => null,
    ])->assertRedirect();

    expect($article->fresh()->title)->toBe('Updated headphones review');
});

it('publishes and unpublishes an article without deleting it', function () {
    $admin = User::factory()->admin()->create();
    $article = Article::factory()->create();

    $this->actingAs($admin)
        ->post(route('admin.articles.publish', $article))
        ->assertRedirect();
    expect($article->fresh()->published_at)->not->toBeNull();

    $this->actingAs($admin)
        ->post(route('admin.articles.unpublish', $article))
        ->assertRedirect();
    expect($article->fresh()->published_at)->toBeNull();
    $this->assertModelExists($article);
});

it('validates article publication fields', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->from(route('admin.articles.create'))
        ->post(route('admin.articles.store'), [
            'title' => '',
            'slug' => 'not valid',
            'rating' => 11,
            'affiliate_url' => 'javascript:alert(1)',
        ])
        ->assertRedirect(route('admin.articles.create'))
        ->assertSessionHasErrors(['title', 'slug', 'image', 'image_alt', 'summary', 'body', 'rating', 'category', 'affiliate_url']);
});

it('renders the admin article index for an administrator', function () {
    $admin = User::factory()->admin()->create();
    Article::factory()->create();

    $this->actingAs($admin)
        ->get(route('admin.articles.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('admin/articles/index')->has('articles.data', 1));
});
