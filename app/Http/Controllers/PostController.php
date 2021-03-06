<?php

namespace App\Http\Controllers;

use App\Post;

class PostController
{
    public function index()
    {
        return view('posts.index');
    }

    public function show(Post $post)
    {
        return view('posts.show', ['post' => $post]);
    }
}
