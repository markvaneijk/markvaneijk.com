@extends('templates.master')

@section('title', 'Posts')
@section('description', 'Posts by Mark van Eijk on Laravel, PHP, React, Inertia and Tailwind CSS.')
@section('grid') grid-cols-[minmax(20px,1fr)_minmax(360px,575px)_minmax(20px,1fr)] @endsection

@section('main')
    <div class="py-14 md:py-24">
        <p class="mb-3 text-sm text-term">~ $ ls ./posts</p>
        <h1 class="mb-10 text-5xl font-bold leading-none font-display">Posts<span class="text-flame">_</span></h1>

        @forelse($posts as $post)
            <article class="mb-8">
                <p class="mb-1 text-sm text-muted">{{ $post->published_at->format('Y-m-d') }}</p>
                <a href="{{ $post->url }}" class="text-xl font-bold t-link">{{ $post->title }}</a>
            </article>
        @empty
            <p class="text-lg leading-relaxed text-muted">
                <span class="text-term">></span> No posts published yet — the first ones about Laravel, React and tinkering are in the works.<br>
                <span class="text-term">></span> In the meantime, check out <a href="{{ route('now') }}" class="t-link">what I'm doing now</a>.
            </p>
        @endforelse
    </div>
@endsection
