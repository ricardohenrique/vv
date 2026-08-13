<?php

use App\Models\Article;
use Inertia\Testing\AssertableInertia as Assert;

it('shows only published articles on the public home page in publication order', function () {
    $older = Article::factory()->published()->create([
        'title' => 'Older review',
        'slug' => 'older-review',
        'published_at' => now()->subWeek(),
    ]);
    $newer = Article::factory()->published()->create([
        'title' => 'Newer review',
        'slug' => 'newer-review',
        'published_at' => now()->subDay(),
    ]);
    Article::factory()->create(['title' => 'Secret draft', 'slug' => 'secret-draft']);

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('articles/index')
            ->has('articles.data', 2)
            ->where('articles.data.0.id', $newer->id)
            ->where('articles.data.1.id', $older->id)
        );
});

it('shows the complete published article', function () {
    $article = Article::factory()->published()->create();

    $this->get(route('articles.show', $article))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('articles/show')
            ->where('article.data.title', $article->title)
            ->where('article.data.body', $article->body)
            ->where('article.data.rating', (float) $article->rating)
        );
});

it('serves packaged demo images without public storage', function () {
    $article = Article::factory()->published()->create([
        'image_path' => 'assets/demo-articles/audio.jpg',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('articles.data.0.id', $article->id)
            ->where('articles.data.0.image_url', asset('assets/demo-articles/audio.jpg'))
        );
});

it('maps legacy seeded images to packaged demo photographs', function () {
    $article = Article::factory()->published()->create([
        'image_path' => 'demo/audio.svg',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('articles.data.0.id', $article->id)
            ->where('articles.data.0.image_url', asset('assets/demo-articles/audio.jpg'))
        );
});

it('does not disclose draft or future articles', function (string $state) {
    $article = Article::factory()->create([
        'published_at' => $state === 'future' ? now()->addDay() : null,
    ]);

    $this->get(route('articles.show', $article))->assertNotFound();
})->with(['draft', 'future']);
