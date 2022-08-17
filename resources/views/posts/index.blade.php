@extends('templates.master')

@section('title', 'Knowledge - '.config('app.name'))

@section('main')
    <h1 class="mb-5 font-extrabold leading-tight text-gray-200 sm:text-4xl">Knowledge</h1>

    @foreach($posts as $post)
    <p class="mb-2"><a href="{{ $post->url }}" class="underline text-emerald-400 hover:text-emerald-200 decoration-2 underline-offset-4">{{ $post->title }}</a></p>
    @endforeach
@endsection
