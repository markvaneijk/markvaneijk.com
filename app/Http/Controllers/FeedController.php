<?php

namespace App\Http\Controllers;

use App\Models\Post;

class FeedController
{
    public function __invoke()
    {
        $posts = Post::published()
            ->orderByDesc('published_at')
            ->get();

        return response()
            ->view('feed', compact('posts'))
            ->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }
}
