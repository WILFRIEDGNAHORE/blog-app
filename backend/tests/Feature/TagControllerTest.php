<?php

namespace Tests\Feature;

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_tags(): void
    {
        Tag::create(['name' => 'Laravel']);
        Tag::create(['name' => 'React']);

        $response = $this->getJson('/api/tags');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    public function test_can_create_tag(): void
    {
        $response = $this->postJson('/api/tags', [
            'name' => 'Docker',
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['name' => 'Docker']);
        $this->assertDatabaseHas('tags', ['name' => 'Docker']);
    }

    public function test_cannot_create_duplicate_tag(): void
    {
        Tag::create(['name' => 'Laravel']);

        $response = $this->postJson('/api/tags', [
            'name' => 'Laravel',
        ]);

        $response->assertStatus(422);
    }

    public function test_can_delete_tag(): void
    {
        $tag = Tag::create(['name' => 'Laravel']);

        $response = $this->deleteJson("/api/tags/{$tag->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }
}
