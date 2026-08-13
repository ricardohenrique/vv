<?php

namespace App\Queries\Articles;

use App\Models\Category;

class ArticleFormOptionsQuery
{
    /** @return array<int, array{id: int, name: string}> */
    public function categories(): array
    {
        return Category::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Category $category): array => ['id' => $category->id, 'name' => $category->name])
            ->all();
    }
}
