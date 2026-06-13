<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use App\Services\ArticleService;

class ArticleController extends Controller
{
    public function __construct(private ArticleService $articleService) {}

    public function index()
    {
        return Article::with(['user', 'tags'])
            ->latest()
            ->paginate(10);
    }

    public function store(StoreArticleRequest $request)
    {
        $article = $this->articleService->create(
            data: [
                'user_id' => 1,
                'title' => $request->validated('title'),
                'content' => $request->validated('content'),
                'status' => $request->validated('status'),
            ],
            image: $request->file('image'),
            tags: $request->validated('tags'),
        );

        return response()->json($article, 201);
    }

    public function show(Article $article)
    {
        return response()->json($article->load(['user', 'tags', 'comments.user']));
    }

    public function update(UpdateArticleRequest $request, Article $article)
    {
        $this->authorize('update', $article);

        $article = $this->articleService->update(
            article: $article,
            data: $request->only(['title', 'content', 'status']),
            image: $request->file('image'),
            tags: $request->validated('tags'),
        );

        return response()->json($article);
    }

    public function destroy(Article $article)
    {
        $this->authorize('delete', $article);

        $this->articleService->delete($article);

        return response()->json(null, 204);
    }
}
