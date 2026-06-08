<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Article $article)
    {
        return response()->json($article->comments()->with('user')->get());
    }

    public function store(Request $request, Article $article)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = $article->comments()->create([
            'user_id' => 1,
            'content' => $request->content,
        ]);

        return response()->json($comment->load('user'), 201);
    }

    public function destroy(Article $article, Comment $comment)
    {
        $comment->delete();

        return response()->json(null, 204);
    }
}
