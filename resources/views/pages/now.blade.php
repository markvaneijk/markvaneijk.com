@extends('templates.master')

@section('grid') grid-cols-[minmax(20px,1fr)_minmax(360px,500px)_minmax(20px,1fr)] @endsection

@section('main')
    <div class="md:py-20">
        <h1 class="mb-10 text-4xl font-medium">Now</h1>
        <x-spotify />
    </div>
@endsection
