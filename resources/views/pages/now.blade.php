@extends('templates.master')

@section('title', 'Now')
@section('description', "What Mark van Eijk is up to right now — what he's listening to, building and running.")
@section('grid') grid-cols-[minmax(20px,1fr)_minmax(360px,500px)_minmax(20px,1fr)] @endsection

@section('main')
    <div class="py-14 md:py-20">
        <p class="mb-3 text-sm text-term">~ $ top -o now</p>
        <h1 class="mb-10 text-5xl font-bold leading-none font-display">Now<span class="text-flame">_</span></h1>
        <x-spotify />
        <x-strava />
        {{-- <x-x /> --}}
        {{-- <x-instagram /> --}}
        {{-- <x-github /> --}}
    </div>
@endsection
