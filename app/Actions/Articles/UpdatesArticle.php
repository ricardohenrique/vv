<?php

namespace App\Actions\Articles;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UpdatesArticle
{
    /** @param array<string, mixed> $data */
    public function __invoke(Article $article, array $data, ?UploadedFile $image): void
    {
        DB::transaction(function () use ($article, $data, $image): void {
            $category = Category::query()->firstOrCreate(
                ['slug' => Str::slug((string) $data['category'])],
                ['name' => Str::title((string) $data['category'])],
            );
            $attributes = [
                ...Arr::except($data, ['category', 'tags', 'image']),
                'category_id' => $category->id,
            ];

            if ($image !== null) {
                $previousImage = $article->image_path;
                $attributes['image_path'] = $image->store('articles', 'public');
                Storage::disk('public')->delete($previousImage);
            }

            $article->update($attributes);
            $article->tags()->sync($this->tagIds((string) ($data['tags'] ?? '')));
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
