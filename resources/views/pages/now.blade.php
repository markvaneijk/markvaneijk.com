@extends('templates.master')

@section('title', 'Now')
@section('description', "What Mark van Eijk is up to right now — what he's listening to, building and running.")
@section('grid') grid-cols-[minmax(20px,1fr)_minmax(360px,575px)_minmax(20px,1fr)] @endsection

@section('main')
    <div class="py-14 md:py-20">
        <p class="mb-3 text-sm text-term">~ $ top -o now</p>
        <h1 class="mb-10 text-5xl font-bold leading-none font-display">Now<span class="text-flame">_</span></h1>

        {{-- One grid rather than nested rows: a widget without data renders
             nothing at all, and then leaves no gap behind either. --}}
        <div class="grid gap-4 sm:grid-cols-2">
            <x-now.now-playing />
            {{-- Paired by subject: the month's distance beside the year's, and
                 what was shipped beside what it earned. The odd one out spans
                 the row instead of leaving a hole beside it. --}}
            <x-now.distance :days="30" label="Distance · last 30 days" />
            <x-now.distance :days="365" label="Distance · last 12 months" />
            <x-now.latest-push />
            <x-now.contributions />
            <x-now.stars />
            <x-now.followers />
            <x-now.top-tracks />
        </div>
    </div>
@endsection
