<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ config('app.name') }}</title>
        <link>{{ url('/') }}</link>
        <description>{{ __('site.feed.description') }}</description>
        <language>{{ app()->getLocale() }}</language>
        <atom:link href="{{ route('feed') }}" rel="self" type="application/rss+xml" />
        @foreach($posts as $post)
        <item>
            <title>{{ $post->title }}</title>
            <link>{{ $post->url }}</link>
            <guid>{{ $post->url }}</guid>
            <pubDate>{{ $post->published_at->toRssString() }}</pubDate>
            <description>{{ $post->excerpt }}</description>
        </item>
        @endforeach
    </channel>
</rss>
