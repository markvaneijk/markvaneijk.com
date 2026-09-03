<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function __invoke(Request $request)
    {
        $posts = Post::published()->get();

        $postsIndex = Url::create(route('posts'));

        // Only claim a modification date we can actually derive — the latest
        // post change; a fabricated "now" timestamp trains crawlers to
        // distrust the field.
        if ($latest = $posts->sortByDesc(fn (Post $post) => $post->updated_at ?? $post->published_at)->first()) {
            $postsIndex->setLastModificationDate($latest->updated_at ?? $latest->published_at);
        }

        return Sitemap::create()
            ->add(Url::create(route('home')))
            ->add(Url::create(route('projects')))
            ->add(Url::create(route('now')))
            ->add($postsIndex)
            ->add($posts);
    }
}
