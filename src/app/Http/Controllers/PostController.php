<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;

class PostController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // 投稿詳細（コメント・いいね数付き）
    public function show($id)
    {
        $post = Post::with(['user', 'likes', 'comments.user'])->findOrFail($id);

        return response()->json([
            'post' => [
                'id' => $post->id,
                'username' => $post->user->name ?? '名無し',
                'content' => $post->content,
            ],
            'likes_count' => $post->likes->count(),
            'liked' => false,
            'comments' => $post->comments->map(function ($comment) {
                return [
                    'user' => $comment->user->name ?? '名無し',
                    'text' => $comment->content,
                ];
            }),
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

        $existingLike = $post->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            $post->likes()->create(['user_id' => $user->id]);
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
