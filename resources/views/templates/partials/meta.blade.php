{{-- Inline @section('x', 'y') values arrive pre-escaped by Blade's startSection();
     decode them here so the {{ }} echoes below escape exactly once. --}}
@php($sections = array_map(fn ($section) => is_string($section) ? html_entity_decode($section, ENT_QUOTES) : $section, app()->view->getSections()))
@php($title = isset($sections['title']) ? $sections['title'].' - '.config('app.name') : config('app.name').' - Laravel Developer from Nijmegen, Netherlands')
@php($description = $sections['description'] ?? 'Mark van Eijk is a full-stack Laravel developer and entrepreneur from Nijmegen, the Netherlands, working with Laravel, React, Inertia and Tailwind CSS.')
@php($image = $sections['image'] ?? asset('images/mark-van-eijk.png'))
@php($isArticle = isset($sections['published_at']))

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
<meta property="og:image" content="{{ $image }}">
<meta property="og:locale" content="en_US">
@if($isArticle)
<meta property="article:author" content="Mark van Eijk">
<meta property="article:published_time" content="{{ $sections['published_at'] }}">
@isset($sections['updated_at'])
<meta property="article:modified_time" content="{{ $sections['updated_at'] }}">
@endisset
@endif

<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@markvaneijk">
<meta name="twitter:creator" content="@markvaneijk">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">

<script type="application/ld+json">
{!! \App\Support\SchemaOrg::person() !!}
</script>
