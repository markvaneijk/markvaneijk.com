@extends('layouts.master')

@section('main')
    <h2>Hello</h2>
    <p>I'm a <a href="https://github.com/markvaneijk" rel="noopener">Web Developer</a>, <a href="https://vormkracht10.nl">entrepeneur</a>, <a href="https://vaneijk.co">company of one</a>, <a href="{{ route('posts') }}">tinkerer</a> and <a href="{{ route('now') }}">currently working</a> on <a href="https://rocketee.rs">Rocketeers</a>.</p>
@endsection
