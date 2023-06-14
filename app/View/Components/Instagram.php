<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Instagram\Api;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class Instagram extends Component
{
    protected $cache;

    protected $client;

    public function __construct()
    {
        $this->cache = new FilesystemAdapter('instagram', 0, storage_path('framework/cache'));
        $this->client = new Api($this->cache);
    }

    public function render()
    {
        $this->client->login(config('services.instagram.username'), config('services.instagram.password'));

        $profile = $this->client->getProfile('markve.jpg');

        $post = collect(
            $profile->getMedias()
        )->map(function ($post) {
            return (object) [
                'id' => $post->getId(),
                'type' => $post->getTypeName(),
                'shortCode' => $post->getShortCode(),
                'link' => $post->getLink(),
                'caption' => $post->getCaption(),
                'location' => $post->getLocation(),
                'image_url' => $post->getThumbnailSrc(),
                'video' => $post->isVideo(),
                'video_url' => $post->getVideoUrl(),
                'date' => $post->getDate(),
                'comments' => $post->getComments(),
                'likes' => $post->getLikes(),
                'views' => $post->getVideoViewCount(),
                'tags' => $post->getHashTags(),
            ];
        })->first();

        dd($post);

        return view('components.instagram', compact('post'));
    }
}
