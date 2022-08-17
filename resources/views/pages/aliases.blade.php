@extends('templates.master')

@section('title', 'My ZSH aliases')
@section('grid') md:grid-cols-[1fr_minmax(360px, 500px)_1fr] @endsection

@section('main')
    <div class="prose prose-xl">
        <h2 class="mb-6 text-xl font-bold leading-tight md:text-4xl">My ZSH aliases</h2>
        <p class="mb-6">In all these years I've collected a lot of aliases to move quickly in the macOS terminal. Because I always find it interesting what others came up with, I share here my current list of aliases.</p>
    </div>
    <x-markdown class="overflow-x-scroll prose prose-xl full-bleed">
        ```bash
{!! $gist !!}
```
    </x-markdown>
@endsection
