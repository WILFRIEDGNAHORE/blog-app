<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index()
    {
        return Article::with(['user', 'tags'])
            ->latest()
            ->paginate(10);
    }

    public function store(StoreArticleRequest $request)
    {
        $data = [
            'user_id' => 1,
            'title' => $request->validated('title'),
            'slug' => Str::slug($request->validated('title')),
            'content' => $request->validated('content'),
            'status' => $request->validated('status') ?? 'draft',
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        $article = Article::create($data);

        if ($request->has('tags')) {
            $article->tags()->sync($request->validated('tags'));
        }

        return response()->json($article->load(['user', 'tags']), 201);
    }

    public function show(Article $article)
    {
        return response()->json($article->load(['user', 'tags', 'comments.user']));
    }

    public function update(UpdateArticleRequest $request, Article $article)
    {
        if ($request->has('title')) {
            $article->slug = Str::slug($request->validated('title'));
        }

        if ($request->hasFile('image')) {
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $article->image = $request->file('image')->store('articles', 'public');
        }

        $article->update($request->only(['title', 'content', 'status']));

        if ($request->has('tags')) {
            $article->tags()->sync($request->validated('tags'));
        }

        return response()->json($article->load(['user', 'tags']));
    }

    public function destroy(Article $article)
    {
        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }

        $article->delete();

        return response()->json(null, 204);
    }
}
