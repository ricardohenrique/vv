<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Articles\UnpublishesArticle;
use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class UnpublishArticleController extends Controller
{
    public function __invoke(Article $article, UnpublishesArticle $unpublish): RedirectResponse
    {
        Gate::authorize('update', $article);
        $unpublish($article);

        return back()->with('status', 'Article unpublished.');
    }
}
