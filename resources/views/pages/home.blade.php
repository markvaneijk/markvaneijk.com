@extends('templates.master')

@section('main')
    <h2 class="text-4xl font-bold">👋🏼 nice to e-meet you, I'm Mark</h2>
    <p>I'm a <a href="https://github.com/markvaneijk" rel="noopener">Web Developer</a>, <a href="https://vormkracht10.nl" rel="noopener" target="_blank">entrepeneur</a>, <a href="https://vaneijk.co" rel="noopener" target="_blank">company of one</a>, <a href="{{ route('post.index') }}">tinkerer</a> and <a href="{{ route('now') }}">currently working</a> on <a href="https://rocketee.rs" rel="noopener" target="_blank">Rocketeers</a>.</p>
@endsection
