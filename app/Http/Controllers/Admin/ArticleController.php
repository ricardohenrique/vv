<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Articles\CreatesArticle;
use App\Actions\Articles\UpdatesArticle;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreArticleRequest;
use App\Http\Requests\Admin\UpdateArticleRequest;
use App\Http\Resources\Articles\ArticleResource;
use App\Models\Article;
use App\Models\User;
use App\Queries\Articles\AdminArticleQuery;
use App\Queries\Articles\ArticleFormOptionsQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ArticleController extends Controller
{
    public function index(AdminArticleQuery $articles): Response
    {
        Gate::authorize('viewAny', Article::class);

        return Inertia::render('admin/articles/index', [
            'articles' => ArticleResource::collection($articles->paginate()),
        ]);
    }

    public function create(ArticleFormOptionsQuery $options): Response
    {
        Gate::authorize('create', Article::class);

        return Inertia::render('admin/articles/create', [
            'categories' => $options->categories(),
        ]);
    }

    public function store(StoreArticleRequest $request, CreatesArticle $create): RedirectResponse
    {
        $user = $request->user();
        $image = $request->file('image');
        abort_unless($user instanceof User && $image instanceof UploadedFile, 422);

        $article = $create($request->validated(), $user, $image);

        return to_route('admin.articles.edit', $article)->with('status', 'Article created as a draft.');
    }

    public function edit(Article $article, AdminArticleQuery $articles, ArticleFormOptionsQuery $options): Response
    {
        Gate::authorize('update', $article);

        return Inertia::render('admin/articles/edit', [
            'article' => ArticleResource::make($articles->detail($article)),
            'categories' => $options->categories(),
        ]);
    }

    public function update(UpdateArticleRequest $request, Article $article, UpdatesArticle $update): RedirectResponse
    {
        $image = $request->file('image');
        $update($article, $request->validated(), $image instanceof UploadedFile ? $image : null);

        return back()->with('status', 'Article updated.');
    }
}
