<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Post;
use App\Http\Middleware\FirebaseAuth;

class FeedIndexTest extends TestCase
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

    protected function setUp(): void
    {
        parent::setUp();

        // FirebaseAuth ミドルウェアをモック
    $this->mock(\App\Http\Middleware\FirebaseAuth::class, function ($mock) {
        $mock->shouldReceive('handle')->andReturnUsing(function ($request, $next) {
            return $next($request);
        });
    });
    }

    public function test_it_returns_posts_list()
    {
        $user = User::factory()->create(['name' => '太郎']);
        $this->actingAs($user);
        Post::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => ['id', 'username', 'content', 'likes', 'uid']
            ]);
    }
}
