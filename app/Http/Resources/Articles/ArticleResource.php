<?php

namespace App\Http\Resources\Articles;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** @mixin Article */
class ArticleResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'image_url' => $this->imageUrl(),
            'image_alt' => $this->imageAlt(),
            'summary' => $this->summary,
            'body' => $this->body,
            'rating' => (float) $this->rating,
            'affiliate_url' => $this->affiliate_url,
            'published_at' => $this->published_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'author' => [
                'id' => $this->author->id,
                'name' => $this->author->name,
            ],
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ],
            'tags' => $this->tags->map(fn ($tag): array => [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
            ])->values()->all(),
        ];
    }

    private function imageUrl(): string
    {
        if (Str::startsWith($this->image_path, 'assets/')) {
            return asset($this->image_path);
        }

        if (Str::startsWith($this->image_path, 'demo/')) {
            $filename = Str::replaceEnd('.svg', '.jpg', Str::after($this->image_path, 'demo/'));

            return asset('assets/demo-articles/'.$filename);
        }

        return Storage::disk('public')->url($this->image_path);
    }

    private function imageAlt(): string
    {
        if (Str::startsWith($this->image_path, 'demo/')) {
            return $this->category->name.' product review photograph';
        }

        return $this->image_alt;
    }
}
