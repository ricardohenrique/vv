<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Articles\PublishesArticle;
use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class PublishArticleController extends Controller
{
    public function __invoke(Article $article, PublishesArticle $publish): RedirectResponse
    {
        Gate::authorize('update', $article);
        $publish($article);

        return back()->with('status', 'Article published.');
    }
}
