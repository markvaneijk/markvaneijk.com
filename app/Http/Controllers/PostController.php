<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PostController
{
    public function __invoke()
    {
        $posts = Post::published()
            ->orderByDesc('published_at')
            ->get();

        return view('posts.index', compact('posts'));
    }

    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }
}
