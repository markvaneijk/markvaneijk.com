@extends('base.app')

@section('main')
    <h2>It works!</h2>
    <p>I'm a <a href="https://github.com/markvaneijk" rel="noopener">Web Developer</a>, <a href="https://vormkracht10.nl" rel="noopener" target="_blank">entrepeneur</a>, <a href="https://vaneijk.co" rel="noopener" target="_blank">company of one</a>, <a href="{{ route('posts') }}">tinkerer</a> and <a href="{{ route('now') }}">currently working</a> on <a href="https://rocketee.rs" rel="noopener" target="_blank">Rocketeers</a>.</p>
@endsection
