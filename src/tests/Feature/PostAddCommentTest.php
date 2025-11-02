<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Post;

class PostAddCommentTest extends TestCase
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

    public function test_user_can_add_comment_to_post()
    {
        $user = User::factory()->create(['firebase_uid' => 'uid777']);
        $post = Post::factory()->create();

        $response = $this->putJson("/api/posts/{$post->id}", [
            'comment' => 'コメントテスト',
            'uid' => 'uid777',
            'name' => 'コメント太郎',
        ]);

        $response->assertStatus(200)
            ->assertJson(['user' => 'コメント太郎', 'text' => 'コメントテスト']);

        $this->assertDatabaseHas('comments', ['content' => 'コメントテスト']);
    }
}
