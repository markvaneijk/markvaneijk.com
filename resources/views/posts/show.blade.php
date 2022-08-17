@extends('templates.master')

@section('title', $post->title. ' - '. config('app.name'))
@section('published_at', $post?->published_at?->format('Y-m-d'))
@section('updated_at', $post?->updated_at?->format('Y-m-d'))

@section('main')
    <h2 class="mb-4 text-lg font-bold tracking-wide text-gray-600 uppercase md:text-xl">Posts</h2>
    <h1 class="mb-5 text-xl font-extrabold leading-tight text-gray-900 md:text-5xl">{{ $post->title }}</h1>
    <p class="mb-5 text-gray-500">
        Published on {{ $post->published_at->format('F j, Y') }}
        @if($post->updated_at)
        <br>Updated on {{ $post->updated_at->format('F j, Y') }}
        @endif
    </p>
    <x-markdown class="prose text-gray-800 md:prose-xl prose-invert prose-a:underline prose-a:text-emerald-400 hover:prose-a:text-emerald-200 prose-a:decoration-2 prose-a:underline-offset-4">
        {!! $post->content !!}
    </x-markdown>
@endsection
