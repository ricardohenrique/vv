<?php

namespace App\Queries\Articles;

use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminArticleQuery
{
    /** @return LengthAwarePaginator<int, Article> */
    public function paginate(): LengthAwarePaginator
    {
        return Article::query()
            ->select(['id', 'author_id', 'category_id', 'title', 'slug', 'image_path', 'image_alt', 'summary', 'body', 'rating', 'affiliate_url', 'published_at', 'created_at', 'updated_at'])
            ->with(['author:id,name', 'category:id,name,slug', 'tags:id,name,slug'])
            ->latest('updated_at')
            ->paginate(20);
    }

    public function detail(Article $article): Article
    {
        return $article->load(['author:id,name', 'category:id,name,slug', 'tags:id,name,slug']);
    }
}
