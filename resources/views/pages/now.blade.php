@extends('templates.master')

@section('title', 'Now')
@section('description', "What Mark van Eijk is up to right now — what he's listening to, building and running.")
@section('grid') grid-cols-[minmax(20px,1fr)_minmax(360px,500px)_minmax(20px,1fr)] @endsection

@section('main')
    <div class="py-14 md:py-20">
        <p class="mb-3 text-sm text-term">~ $ top -o now</p>
        <h1 class="mb-10 text-5xl font-bold leading-none font-display">Now<span class="text-flame">_</span></h1>

        {{-- One grid rather than nested rows: a widget without data renders
             nothing at all, and then leaves no gap behind either. --}}
        <div class="grid gap-4 sm:grid-cols-2">
            <x-now.now-playing />
            <x-now.followers />
            <x-now.distance />
            <x-now.top-tracks />
        </div>
    </div>
@endsection
