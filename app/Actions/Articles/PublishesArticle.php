<?php

namespace App\Actions\Articles;

use App\Models\Article;

class PublishesArticle
{
    public function __invoke(Article $article): void
    {
        if ($article->published_at === null) {
            $article->update(['published_at' => now()]);
        }
    }
}
