<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Mockery;
use App\Models\User;
use Kreait\Firebase\Auth as FirebaseAuth;

class FirebaseAuthTest extends TestCase
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

    public function test_verify_token_creates_user_and_returns_success()
    {
        $mock = Mockery::mock(FirebaseAuth::class);
        $mock->shouldReceive('verifyIdToken')
            ->andReturn((object)['claims' => fn() => collect(['sub' => 'abc123'])]);
        $mock->shouldReceive('getUser')
            ->andReturn((object)['displayName' => 'テストユーザー']);

        $this->app->instance(FirebaseAuth::class, $mock);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer dummy_token',
        ])->postJson('/api/auth/verify');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['user' => ['id', 'name']]);

        $this->assertDatabaseHas('users', ['firebase_uid' => 'abc123']);
    }
}
