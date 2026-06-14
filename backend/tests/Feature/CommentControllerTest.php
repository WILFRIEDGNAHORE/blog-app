<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Admin',
            'email' => 'admin@blog.com',
            'password' => bcrypt('password'),
        ]);

        $this->article = Article::create([
            'user_id' => $this->user->id,
            'title' => 'Mon article',
            'slug' => 'mon-article',
            'content' => 'Contenu',
            'status' => 'published',
        ]);
    }

    public function test_can_get_comments_of_article(): void
    {
        $this->article->comments()->create([
            'user_id' => $this->user->id,
            'content' => 'Premier commentaire',
        ]);
        $this->article->comments()->create([
            'user_id' => $this->user->id,
            'content' => 'Deuxieme commentaire',
        ]);

        $response = $this->getJson("/api/articles/{$this->article->id}/comments");

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    public function test_can_create_comment(): void
    {
        $response = $this->actingAs($this->user)->postJson("/api/articles/{$this->article->id}/comments", [
            'content' => 'Super article !',
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['content' => 'Super article !']);
        $this->assertDatabaseHas('comments', [
            'article_id' => $this->article->id,
            'content' => 'Super article !',
        ]);
    }

    public function test_cannot_create_comment_without_content(): void
    {
        $response = $this->postJson("/api/articles/{$this->article->id}/comments", []);

        $response->assertStatus(422);
    }

    public function test_can_delete_comment(): void
    {
        $comment = $this->article->comments()->create([
            'user_id' => $this->user->id,
            'content' => 'Commentaire a supprimer',
        ]);

        $response = $this->deleteJson("/api/articles/{$this->article->id}/comments/{$comment->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
