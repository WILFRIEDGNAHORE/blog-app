<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Models\Tag;

class TagController extends Controller
{
    public function index()
    {
        return response()->json(Tag::withCount('articles')->get());
    }

    public function store(StoreTagRequest $request)
    {
        $tag = Tag::create($request->validated());

        return response()->json($tag, 201);
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json(null, 204);
    }
}
