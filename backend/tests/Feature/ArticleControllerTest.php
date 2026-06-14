<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Tag $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Admin',
            'email' => 'admin@blog.com',
            'password' => bcrypt('password'),
        ]);

        $this->tag = Tag::create(['name' => 'Laravel']);
    }

    public function test_can_get_all_articles_paginated(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            Article::create([
                'user_id' => $this->user->id,
                'title' => "Article {$i}",
                'slug' => "article-{$i}",
                'content' => "Contenu {$i}",
                'status' => 'published',
            ]);
        }

        $response = $this->getJson('/api/articles');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'current_page',
            'last_page',
            'per_page',
            'total',
        ]);
        $response->assertJsonPath('total', 15);
    }

    public function test_can_create_article(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/articles', [
            'title' => 'Mon article',
            'content' => 'Contenu de larticle',
            'status' => 'draft',
            'tags' => [$this->tag->id],
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['title' => 'Mon article']);
        $response->assertJsonFragment(['slug' => 'mon-article']);
        $response->assertJsonStructure(['tags']);
        $this->assertDatabaseHas('articles', ['title' => 'Mon article']);
    }

    public function test_cannot_create_article_without_title(): void
    {
        $response = $this->postJson('/api/articles', [
            'content' => 'Contenu sans titre',
        ]);

        $response->assertStatus(422);
    }

    public function test_cannot_create_article_with_invalid_status(): void
    {
        $response = $this->postJson('/api/articles', [
            'title' => 'Mon article',
            'content' => 'Contenu',
            'status' => 'invalide',
        ]);

        $response->assertStatus(422);
    }

    public function test_can_show_article_with_relations(): void
    {
        $article = Article::create([
            'user_id' => $this->user->id,
            'title' => 'Mon article',
            'slug' => 'mon-article',
            'content' => 'Contenu',
            'status' => 'published',
        ]);
        $article->tags()->attach($this->tag->id);

        $response = $this->getJson("/api/articles/{$article->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Mon article']);
        $response->assertJsonStructure(['user', 'tags', 'comments']);
    }

    public function test_can_update_article_status(): void
    {
        $article = Article::create([
            'user_id' => $this->user->id,
            'title' => 'Mon article',
            'slug' => 'mon-article',
            'content' => 'Contenu',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->putJson("/api/articles/{$article->id}", [
            'status' => 'published',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'status' => 'published',
        ]);
    }

    public function test_can_update_article_tags(): void
    {
        $article = Article::create([
            'user_id' => $this->user->id,
            'title' => 'Mon article',
            'slug' => 'mon-article',
            'content' => 'Contenu',
            'status' => 'draft',
        ]);
        $article->tags()->attach($this->tag->id);

        $newTag = Tag::create(['name' => 'React']);

        $response = $this->actingAs($this->user)->putJson("/api/articles/{$article->id}", [
            'tags' => [$newTag->id],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('article_tag', [
            'article_id' => $article->id,
            'tag_id' => $newTag->id,
        ]);
        $this->assertDatabaseMissing('article_tag', [
            'article_id' => $article->id,
            'tag_id' => $this->tag->id,
        ]);
    }

    public function test_can_delete_article(): void
    {
        $article = Article::create([
            'user_id' => $this->user->id,
            'title' => 'Mon article',
            'slug' => 'mon-article',
            'content' => 'Contenu',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/api/articles/{$article->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function test_can_create_article_with_image(): void
    {
        Storage::fake('public');

        $file = File::image('photo.jpg');

        $response = $this->actingAs($this->user)->postJson('/api/articles', [
            'title' => 'Article avec image',
            'content' => 'Contenu',
            'image' => $file,
        ]);

        $response->assertStatus(201);

        $article = Article::first();
        Storage::disk('public')->assertExists($article->image);
    }
}
