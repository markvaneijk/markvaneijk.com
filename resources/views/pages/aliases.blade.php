@extends('templates.master')

@section('title', 'My ZSH aliases')
@section('description', 'The ZSH aliases Mark van Eijk uses to move quickly in the macOS terminal.')
@section('robots', 'noindex,follow')
@section('grid') md:grid-cols-[1fr_minmax(360px, 500px)_1fr] @endsection

@section('main')
    <div class="prose prose-xl prose-invert pt-14 md:pt-20 prose-headings:font-display">
        <h1 class="text-4xl leading-none">My ZSH aliases<span class="text-flame">_</span></h1>
        <p class="mb-6">In all these years I've collected a lot of aliases to move quickly in the macOS terminal. Because I always find it interesting what others came up with, I share here my current list of aliases.</p>
    </div>
    <x-markdown class="overflow-x-scroll prose prose-xl prose-invert full-bleed">
        ```bash
{!! $gist !!}
```
    </x-markdown>
@endsection
