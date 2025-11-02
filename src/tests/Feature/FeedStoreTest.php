<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Post;

class FeedStoreTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_user_can_create_post()
    {
        $response = $this->postJson('/api/posts', [
            'content' => 'テスト投稿です',
            'uid' => 'uid123',
            'name' => 'テストユーザー',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['id', 'username', 'content', 'likes', 'uid']);

        $this->assertDatabaseHas('posts', ['content' => 'テスト投稿です']);
    }
}
