{{-- Inline @section('x', 'y') values arrive pre-escaped by Blade's startSection();
     decode them here so the {{ }} echoes below escape exactly once. --}}
@php($sections = array_map(fn ($section) => is_string($section) ? html_entity_decode($section, ENT_QUOTES) : $section, app()->view->getSections()))
@php($title = isset($sections['title']) ? $sections['title'].' - '.config('app.name') : config('app.name').' - '.__('site.meta.tagline'))
@php($description = $sections['description'] ?? __('site.meta.description'))
@php($isArticle = isset($sections['published_at']))

{{-- The same page on the other domain: the two sites carry the same paths and
     differ only in the language they are written in. --}}
@php($alternates = \App\Support\Locales::alternates(request()->path()))

{{-- No @section('image') means the share image is drawn for this page, by
     <x-og-image-tags> below. --}}
@php($generatedImage = ! isset($sections['image']))

<meta charset="utf-8">
<title>{{ $title }}</title>
<link rel="canonical" href="{{ url()->current() }}">
@foreach($alternates as $locale => $url)
<link rel="alternate" hreflang="{{ $locale }}" href="{{ $url }}">
@endforeach
<link rel="alternate" hreflang="x-default" href="{{ $alternates[\App\Support\Locales::default()] }}">
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
{{-- The card is drawn for this page: the component signs a URL onto the
     og-image route, which renders vendor/og-image/template.blade.php in
     headless Chrome the first time a crawler asks for it and caches the result.
     Every attribute below is printed on the card and the cache is keyed on
     them, so they have to stay stable per page. Blade puts each one through
     sanitizeComponentAttribute() on the way in, so the text is HTML-encoded by
     the time it is signed — the card template decodes it once before printing.

     The component writes the image size and the Twitter card and image along
     with the og:image, which is why the Twitter block below carries neither. --}}
@if($generatedImage)
<x-og-image-tags
    :title="$sections['title'] ?? config('app.name')"
    :description="Str::limit($description, 120)"
    :path="request()->path()"
    :date="$sections['published_at'] ?? ''"
/>
<meta property="og:image:alt" content="{{ $title }}">
@else
<meta property="og:image" content="{{ $sections['image'] }}">
<meta name="twitter:card" content="summary">
<meta name="twitter:image" content="{{ $sections['image'] }}">
@endif
<meta property="og:locale" content="{{ \App\Support\Locales::openGraph(app()->getLocale()) }}">
@foreach(array_keys($alternates) as $alternate)
@continue($alternate === app()->getLocale())
<meta property="og:locale:alternate" content="{{ \App\Support\Locales::openGraph($alternate) }}">
@endforeach
@if($isArticle)
<meta property="article:author" content="Mark van Eijk">
<meta property="article:published_time" content="{{ $sections['published_at'] }}">
@isset($sections['updated_at'])
<meta property="article:modified_time" content="{{ $sections['updated_at'] }}">
@endisset
@endif

<meta name="twitter:site" content="@markvaneijk">
<meta name="twitter:creator" content="@markvaneijk">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">

<script type="application/ld+json">
{!! \App\Support\SchemaOrg::person() !!}
</script>
