<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Post;

class PostLikeToggleTest extends TestCase
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

    public function test_like_and_unlike_toggle_works()
    {
        $user = User::factory()->create(['firebase_uid' => 'uid555']);
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson("/api/posts/{$post->id}/like", [
            'uid' => 'uid555',
            'name' => 'テスト太郎'
        ]);
        $response->assertStatus(200)->assertJson(['liked' => true]);

        $response = $this->postJson("/api/posts/{$post->id}/like", [
            'uid' => 'uid555',
        ]);
        $response->assertStatus(200)->assertJson(['liked' => false]);
    }
}
