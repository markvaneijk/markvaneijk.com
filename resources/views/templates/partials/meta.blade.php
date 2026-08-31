{{-- Inline @section('x', 'y') values arrive pre-escaped by Blade's startSection();
     decode them here so the {{ }} echoes below escape exactly once. --}}
@php($sections = array_map(fn ($section) => is_string($section) ? html_entity_decode($section, ENT_QUOTES) : $section, app()->view->getSections()))
@php($title = isset($sections['title']) ? $sections['title'].' - '.config('app.name') : config('app.name').' - Laravel Developer from Nijmegen, Netherlands')
@php($description = $sections['description'] ?? 'Mark van Eijk is a full-stack Laravel developer and entrepreneur from Nijmegen, the Netherlands, working with Laravel, React, Inertia and Tailwind CSS.')
@php($isArticle = isset($sections['published_at']))

{{-- No @section('image') means the share image is drawn for this page: a signed
     URL onto the og-image route, which renders vendor/og-image/template.blade.php
     in headless Chrome the first time a crawler asks for it and caches the
     result. The parameters are what the card prints, so they have to stay
     byte-for-byte stable per page — they are what the cache is keyed on. --}}
@php($generatedImage = ! isset($sections['image']))
@php($image = $sections['image'] ?? og(array_filter([
    'title' => $sections['title'] ?? config('app.name'),
    'description' => Str::limit($description, 120),
    'path' => request()->path(),
    'date' => $sections['published_at'] ?? '',
])))

<meta charset="utf-8">
<title>{{ $title }}</title>
<link rel="canonical" href="{{ url()->current() }}">
<meta name="description" content="{{ $description }}">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="apple-mobile-web-app-title" content="Mark van Eijk">
<meta property="author" content="Mark van Eijk">
<meta name="generator" content="Rocketeers">
<meta name="robots" content="@yield('robots', 'index,follow')">
<meta name="theme-color" content="#0B0E14">

<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
<link rel="alternate" type="application/rss+xml" title="{{ config('app.name') }}" href="{{ route('feed') }}">

<meta property="og:type" content="{{ $isArticle ? 'article' : 'website' }}">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ url()->current() }}">
{{-- Unescaped on purpose. The signature covers the query string exactly as it
     was signed, so escaping the separators into &amp; hands anything that reads
     the attribute literally three junk parameters (amp;path, amp;title,
     amp;.png) and a 403. A raw & is not an ambiguous ampersand here — none of
     the parameter names is a named character reference — and the URL itself is
     built by url()->signedRoute(), which percent-encodes every value. --}}
<meta property="og:image" content="{!! $image !!}">
@if($generatedImage)
<meta property="og:image:type" content="{{ \Backstage\OgImage\Laravel\Facades\OgImage::getImageMimeType() }}">
<meta property="og:image:width" content="{{ config('og-image.width') }}">
<meta property="og:image:height" content="{{ config('og-image.height') }}">
<meta property="og:image:alt" content="{{ $title }}">
@endif
<meta property="og:locale" content="en_US">
@if($isArticle)
<meta property="article:author" content="Mark van Eijk">
<meta property="article:published_time" content="{{ $sections['published_at'] }}">
@isset($sections['updated_at'])
<meta property="article:modified_time" content="{{ $sections['updated_at'] }}">
@endisset
@endif

{{-- 1200x630 is the large card; the small one crops it to a square thumbnail. --}}
<meta name="twitter:card" content="{{ $generatedImage ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:site" content="@markvaneijk">
<meta name="twitter:creator" content="@markvaneijk">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{!! $image !!}">

<script type="application/ld+json">
{!! \App\Support\SchemaOrg::person() !!}
</script>
