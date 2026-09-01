@extends('templates.master')

@section('title', __('site.now.title'))
@section('description', __('site.now.description'))
@section('grid') grid-cols-[minmax(20px,1fr)_minmax(360px,575px)_minmax(20px,1fr)] @endsection

@section('main')
    <div class="py-14 md:py-20">
        <p class="mb-3 text-sm text-term">~ $ top -o now</p>
        <h1 class="mb-10 text-5xl font-bold leading-none font-display">{{ __('site.now.title') }}<span class="text-flame">_</span></h1>

        {{-- Every widget is handed the language it is being read in, because
             that is part of what it caches its HTML under — see Widget. It has
             to be spelled out here: the scheduler draws the same widgets
             outside any request, where there is no host to ask. --}}
        @php($locale = app()->getLocale())

        {{-- One grid rather than nested rows: a widget without data renders
             nothing at all, and then leaves no gap behind either. --}}
        <div class="grid gap-4 sm:grid-cols-2">
            <x-now.now-playing :locale="$locale" />
            {{-- Paired by subject: the month's distance beside the year's, and
                 what was shipped beside what it earned. The odd one out spans
                 the row instead of leaving a hole beside it. --}}
            <x-now.distance :locale="$locale" :days="30" />
            <x-now.distance :locale="$locale" :days="365" />
            <x-now.latest-push :locale="$locale" />
            <x-now.contributions :locale="$locale" />
            <x-now.stars :locale="$locale" />
            <x-now.followers :locale="$locale" />
            <x-now.top-tracks :locale="$locale" />
        </div>
    </div>
@endsection
