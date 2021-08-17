@extends('base.app')

@section('main')
    <h1>{{ $post->title }}</h1>
    {{ $post->contents }}
@endsection
