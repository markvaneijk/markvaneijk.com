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
        return Sitemap::create()
            ->add(Url::create(route('home')))
            ->add(Url::create(route('aliases')))
            ->add(Url::create(route('now')))
            ->add(Url::create(route('posts')))
            ->add(Url::create(route('uses')))
            ->add(Post::published()->get());
    }
}
