<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Post;
use App\Models\User;

class FeedController extends Controller
{
    public function index()
    {
        $posts = Post::with('user', 'likes')->latest()->get();
        return response()->json($posts->map(function($post) {
            return [
                'id' => $post->id,
                'username' => $post->user->name ?? '名無し',
                'content' => $post->content,
                'likes' => $post->likes->count(),
                'uid' => $post->user->firebase_uid ?? '',
            ];
        }));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|max:120',
            'uid' => 'required',
        ]);

        $user = User::firstOrCreate(
            ['firebase_uid' => $request->uid],
            ['name' => $request->name ?? '名無し']
        );

        $post = Post::create([
            'user_id' => $user->id,
            'content' => $request->content,
        ]);

        return response()->json([
            'id' => $post->id,
            'username' => $user->name,
            'content' => $post->content,
            'likes' => 0,
            'uid' => $request->uid,
            'name' => $user->name,
        ]);
    }
}
