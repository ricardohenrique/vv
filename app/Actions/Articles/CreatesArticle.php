<?php

namespace App\Actions\Articles;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreatesArticle
{
    /** @param array<string, mixed> $data */
    public function __invoke(array $data, User $author, UploadedFile $image): Article
    {
        return DB::transaction(function () use ($data, $author, $image): Article {
            $category = Category::query()->firstOrCreate(
                ['slug' => Str::slug((string) $data['category'])],
                ['name' => Str::title((string) $data['category'])],
            );

            $article = Article::query()->create([
                ...Arr::except($data, ['category', 'tags', 'image']),
                'author_id' => $author->id,
                'category_id' => $category->id,
                'image_path' => $image->store('articles', 'public'),
            ]);

            $article->tags()->sync($this->tagIds((string) ($data['tags'] ?? '')));

            return $article;
        });
    }

    /** @return array<int, int> */
    private function tagIds(string $tags): array
    {
        return collect(explode(',', $tags))
            ->map(fn (string $tag): string => Str::squish($tag))
            ->filter()
            ->unique(fn (string $tag): string => Str::lower($tag))
            ->take(10)
            ->map(fn (string $tag): int => Tag::query()->firstOrCreate(
                ['slug' => Str::slug($tag)],
                ['name' => Str::title($tag)],
            )->id)
            ->values()
            ->all();
    }
}
