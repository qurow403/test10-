<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;

class PostController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // 投稿詳細（コメント・いいね数付き）
    public function show($id)
    {
        $post = (object)[
            'id' => $id,
            'title' => 'test1',
            'content' => 'comment',
            'likes' => collect([]), // 空のコレクション
            'comments' => collect([
                (object)[
                    'user' => (object)['name' => 'test1'],
                    'content' => 'comment'
                ]
            ]),
        ];

        return response()->json([
            'post' => $post,
            'likes_count' => $post->likes->count(),
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
        $user = $request->user();
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
        ]);

        $post = Post::findOrFail($id);

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $request->comment,
        ]);

        return response()->json([
            'user' => $request->user()->name,
            'text' => $comment->content,
        ]);
    }
}
