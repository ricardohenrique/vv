<?php

namespace App\Queries\Articles;

use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PublicArticleQuery
{
    /** @return LengthAwarePaginator<int, Article> */
    public function paginate(): LengthAwarePaginator
    {
        return Article::query()
            ->published()
            ->select(['id', 'author_id', 'category_id', 'title', 'slug', 'image_path', 'image_alt', 'summary', 'body', 'rating', 'affiliate_url', 'published_at', 'created_at', 'updated_at'])
            ->with(['author:id,name', 'category:id,name,slug', 'tags:id,name,slug'])
            ->latest('published_at')
            ->latest('id')
            ->paginate(12);
    }

    public function detail(Article $article): Article
    {
        abort_unless($article->published_at !== null && $article->published_at->isPast(), 404);

        return $article->load(['author:id,name', 'category:id,name,slug', 'tags:id,name,slug']);
    }
}
