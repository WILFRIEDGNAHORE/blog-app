<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Article;
use App\Models\Comment;

class CommentController extends Controller
{
    public function index(Article $article)
    {
        return response()->json($article->comments()->with('user')->get());
    }

    public function store(StoreCommentRequest $request, Article $article)
    {
        $comment = $article->comments()->create([
            'user_id' => 1,
            'content' => $request->validated('content'),
        ]);

        return response()->json($comment->load('user'), 201);
    }

    public function destroy(Article $article, Comment $comment)
    {
        $comment->delete();

        return response()->json(null, 204);
    }
}
