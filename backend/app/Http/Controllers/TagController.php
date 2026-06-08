<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        return response()->json(Tag::withCount('articles')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
        ]);

        $tag = Tag::create(['name' => $request->name]);

        return response()->json($tag, 201);
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json(null, 204);
    }
}
