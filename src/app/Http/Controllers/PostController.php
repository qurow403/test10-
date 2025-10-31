<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // 投稿詳細（コメント・いいね数付き）
    public function show(Request $request, $id)
    {
        $post = Post::with(['user', 'likes', 'comments.user'])->findOrFail($id);

        $liked = false;
        if ($request->has('firebase_uid')) {
            $user = User::where('firebase_uid', $request->firebase_uid)->first();
            if ($user) {
                $liked = $post->likes()->where('user_id', $user->id)->exists();
            }
        }

        return response()->json([
            'post' => [
                'id' => $post->id,
                'username' => $post->user->name ?? '名無し',
                'content' => $post->content,
            ],
            'likes_count' => $post->likes->count(),
            'liked' => $liked,
            'comments' => $post->comments->map(fn($c) => [
                'user' => $c->user->name ?? '名無し',
                'text' => $c->content,
            ]),
        ]);
    }

    public function toggleLike(Request $request, $id)
    {
        $request->validate(['uid' => 'required']);

        $user = User::firstOrCreate(
            ['firebase_uid' => $request->uid],
            ['name' => $request->name ?? '名無し']
        );

        $post = Post::findOrFail($id);

        if ($post->likes()->where('user_id', $user->id)->exists()) {
            $post->likes()->detach($user->id);
            $liked = false;
        } else {
            $post->likes()->attach($user->id);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $post->likes()->count(),
        ]);
    }

    /**
     * コメント投稿（PUT）
     */
    public function addComment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|max:120',
            'uid' => 'required',
        ]);

        $user = User::firstOrCreate(
            ['firebase_uid' => $request->uid],
            ['name' => $request->name ?? '名無し']
        );

        $post = Post::findOrFail($id);

        $comment = $post->comments()->create([
            'user_id' => $user->id,
            'content' => $request->comment,
        ]);

        return response()->json([
            'user' => $user->name,
            'text' => $comment->content,
        ]);
    }
}
