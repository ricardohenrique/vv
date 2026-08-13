<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\FreshReviewDatabaseSeeder;
use Illuminate\Support\Facades\Artisan;

it('registers the fresh database command', function () {
    expect(Artisan::all())->toHaveKey('db:fresh-seed');
});

it('creates the complete review demo dataset', function () {
    $this->seed(FreshReviewDatabaseSeeder::class);

    expect(Article::query()->count())->toBe(500)
        ->and(Article::query()->whereNotNull('published_at')->count())->toBe(500)
        ->and(Category::query()->count())->toBe(10)
        ->and(User::query()->count())->toBe(5)
        ->and(Article::query()->distinct()->count('category_id'))->toBe(10)
        ->and(Article::query()->distinct()->count('author_id'))->toBe(5);

    expect(Article::query()->select('category_id')->selectRaw('count(*) as aggregate')->groupBy('category_id')->pluck('aggregate')->all())
        ->each->toBe(50);
    expect(Article::query()->select('author_id')->selectRaw('count(*) as aggregate')->groupBy('author_id')->pluck('aggregate')->all())
        ->each->toBe(100);
    expect(Article::query()->withCount('tags')->get()->pluck('tags_count'))
        ->each->toBeBetween(3, 10);
    expect(Article::query()->distinct()->count('image_path'))->toBe(10)
        ->and(public_path('assets/demo-articles/audio.jpg'))->toBeFile();
});

it('refuses to rebuild the database outside local and testing environments', function () {
    app()->instance('env', 'production');

    $this->artisan('db:fresh-seed')
        ->expectsOutputToContain('limited to local and testing environments')
        ->assertFailed();
});
