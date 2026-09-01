@extends('templates.master')

@section('title', __('site.aliases.title'))
@section('description', __('site.aliases.description'))
@section('robots', 'noindex,follow')
@section('grid') md:grid-cols-[1fr_minmax(360px, 500px)_1fr] @endsection

@section('main')
    <div class="prose prose-xl prose-invert pt-14 md:pt-20 prose-headings:font-display">
        <h1 class="text-4xl leading-none">{{ __('site.aliases.title') }}<span class="text-flame">_</span></h1>
        <p class="mb-6">{{ __('site.aliases.intro') }}</p>
    </div>
    <x-markdown class="overflow-x-scroll prose prose-xl prose-invert full-bleed">
        ```bash
{!! $gist !!}
```
    </x-markdown>
@endsection
