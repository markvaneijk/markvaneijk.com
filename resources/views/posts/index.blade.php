@extends('templates.master')

@section('title', __('site.posts.title'))
@section('description', __('site.posts.description'))
@section('grid') grid-cols-[minmax(20px,1fr)_minmax(360px,575px)_minmax(20px,1fr)] @endsection

@section('main')
    <div class="py-14 md:py-24">
        <p class="mb-3 text-sm text-term">~ $ ls ./posts</p>
        <h1 class="mb-10 text-5xl font-bold leading-none font-display">{{ __('site.posts.title') }}<span class="text-flame">_</span></h1>

        @forelse($posts as $post)
            <article class="mb-8">
                <p class="mb-1 text-sm text-muted">{{ $post->published_at->format('Y-m-d') }}</p>
                <a href="{{ $post->url }}" class="text-xl font-bold t-link">{{ $post->title }}</a>
            </article>
        @empty
            <p class="text-lg leading-relaxed text-muted">
                <span class="text-term">></span> {{ __('site.posts.empty') }}<br>
                <span class="text-term">></span> {!! __('site.posts.empty_meanwhile', ['now' => route('now')]) !!}
            </p>
        @endforelse
    </div>
@endsection
