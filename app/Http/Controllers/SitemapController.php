<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SitemapController extends Controller
{
    public function __invoke(Request $request)
    {
        $posts = Post::published()->get();

        $urls = [
            ['loc' => route('home')],
            ['loc' => route('now')],
            ['loc' => route('posts')],
        ];

        // Only claim a modification date we can actually derive — the latest
        // post change; a fabricated "now" timestamp trains crawlers to
        // distrust the field.
        if ($latest = $posts->sortByDesc(fn (Post $post) => $post->updated_at ?? $post->published_at)->first()) {
            $urls[2]['lastmod'] = $latest->updated_at ?? $latest->published_at;
        }

        foreach ($posts as $post) {
            $urls[] = [
                'loc' => $post->url,
                'lastmod' => $post->updated_at ?? $post->published_at,
            ];
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'text/xml; charset=UTF-8');
    }
}
