<?php

namespace App\Actions\Articles;

use App\Models\Article;

class UnpublishesArticle
{
    public function __invoke(Article $article): void
    {
        $article->update(['published_at' => null]);
    }
}
