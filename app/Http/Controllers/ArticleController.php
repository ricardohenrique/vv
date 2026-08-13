<?php

namespace App\Http\Controllers;

use App\Http\Resources\Articles\ArticleResource;
use App\Models\Article;
use App\Queries\Articles\PublicArticleQuery;
use Inertia\Inertia;
use Inertia\Response;

class ArticleController extends Controller
{
    public function index(PublicArticleQuery $articles): Response
    {
        return Inertia::render('articles/index', [
            'articles' => ArticleResource::collection($articles->paginate()),
        ]);
    }

    public function show(Article $article, PublicArticleQuery $articles): Response
    {
        return Inertia::render('articles/show', [
            'article' => ArticleResource::make($articles->detail($article)),
        ]);
    }
}
