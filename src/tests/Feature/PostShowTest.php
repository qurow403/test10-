<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class PostShowTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_post_detail_returns_correct_data()
    {
        $user = User::factory()->create(['name' => '山田太郎']);
        $post = Post::factory()->create(['user_id' => $user->id, 'content' => '投稿本文']);
        Comment::factory()->create(['post_id' => $post->id, 'user_id' => $user->id, 'content' => 'コメント本文']);

        $response = $this->getJson("/api/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'post' => ['id', 'username', 'content'],
                'likes_count',
                'comments' => [['user', 'text']]
            ]);
    }
}
