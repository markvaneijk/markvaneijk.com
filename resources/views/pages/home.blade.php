@extends('templates.master')

@section('main')
    <h2 class="text-4xl font-bold leading-tight mb-3">Hallo! 👋🏼<br>I'm <x-link href="https://twitter.com/markvaneijk" rel="noopener" target="_blank">Mark</x-link>.</h2>
    <p class="leading-relaxed text-2xl">
        I'm <x-link href="https://github.com/markvaneijk" rel="noopener" target="_blank">🧑🏻‍💻&nbsp;Web Developer</x-link>,
        <x-link href="https://vormkracht10.nl" rel="noopener" target="_blank">entrepeneur</x-link>,
        <x-link href="https://vaneijk.co" rel="noopener" target="_blank">company of one</x-link>,
        <x-link href="{{ route('post.index') }}">tinkerer</x-link> and
        <x-link href="{{ route('now') }}">currently working</x-link>
        on <x-link href="https://rocketee.rs">🚀&nbsp;Rocketeers</x-link> from <x-link href="https://www.nijmegen.nl">🇳🇱&nbsp;Nijmegen.</x-link>
</p>
    <div class="mt-6 grid md:grid-cols-3 gap-4 text-center">
        <x-button href="mailto:m@rkvaneijk.nl">E-mail</x-button>
        <x-button href="https://twitter.com/markvaneijk">Twitter</x-button>
        <x-button href="https://linkedin.com/in/markveijk">LinkedIn</x-button>
    </div>
@endsection
