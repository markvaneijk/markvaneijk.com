@extends('templates.master')

@section('title', $post->title)
@section('description', $post->excerpt)
@section('published_at', $post?->published_at?->format('Y-m-d'))
@if($post->updated_at)
@section('updated_at', $post->updated_at->format('Y-m-d'))
@endif

@push('structured-data')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => $post->title,
    'description' => $post->excerpt,
    'url' => $post->url,
    'datePublished' => $post->published_at->format('Y-m-d'),
    'dateModified' => ($post->updated_at ?? $post->published_at)->format('Y-m-d'),
    'author' => ['@id' => url('/').'#mark'],
    'mainEntityOfPage' => $post->url,
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('main')
    <div class="py-14 md:py-20">
        <p class="mb-4 text-sm text-muted">
            <span class="text-term">$</span> published {{ $post->published_at->format('Y-m-d') }}
            @if($post->updated_at && ! $post->updated_at->isSameDay($post->published_at))
            · updated {{ $post->updated_at->format('Y-m-d') }}
            @endif
        </p>
        <h1 class="mb-10 text-4xl font-bold leading-tight font-display md:text-5xl">{{ $post->title }}<span class="text-flame">_</span></h1>
        <x-markdown class="prose prose-invert md:prose-xl prose-headings:font-display prose-a:t-link">
            {!! $post->content !!}
        </x-markdown>
    </div>
@endsection
