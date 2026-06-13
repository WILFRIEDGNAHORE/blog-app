<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleService
{
    public function create(array $data, ?UploadedFile $image = null, ?array $tags = null): Article
    {
        $data['slug'] = Str::slug($data['title']);
        $data['status'] = $data['status'] ?? 'draft';

        if ($image) {
            $data['image'] = $image->store('articles', 'public');
        }

        $article = Article::create($data);

        if ($tags !== null) {
            $article->tags()->sync($tags);
        }

        return $article->load(['user', 'tags']);
    }

    public function update(Article $article, array $data, ?UploadedFile $image = null, ?array $tags = null): Article
    {
        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        if ($image) {
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $data['image'] = $image->store('articles', 'public');
        }

        $article->update($data);

        if ($tags !== null) {
            $article->tags()->sync($tags);
        }

        return $article->load(['user', 'tags']);
    }

    public function delete(Article $article): void
    {
        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }

        $article->delete();
    }
}
