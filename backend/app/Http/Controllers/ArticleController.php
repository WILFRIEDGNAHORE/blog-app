<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with(['user', 'tags']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }

        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('name', $request->tag);
            });
        }

        return $query->latest()->paginate(10);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'sometimes|in:draft,published',
            'tags' => 'sometimes|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $article = Article::create([
            'user_id' => 1,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'status' => $request->status ?? 'draft',
        ]);

        if ($request->has('tags')) {
            $article->tags()->sync($request->tags);
        }

        return response()->json($article->load(['user', 'tags']), 201);
    }

    public function show(Article $article)
    {
        return response()->json($article->load(['user', 'tags', 'comments.user']));
    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'status' => 'sometimes|in:draft,published',
            'tags' => 'sometimes|array',
            'tags.*' => 'exists:tags,id',
        ]);

        if ($request->has('title')) {
            $article->slug = Str::slug($request->title);
        }

        $article->update($request->only(['title', 'content', 'status']));

        if ($request->has('tags')) {
            $article->tags()->sync($request->tags);
        }

        return response()->json($article->load(['user', 'tags']));
    }

    public function destroy(Article $article)
    {
        $article->delete();

        return response()->json(null, 204);
    }
}
