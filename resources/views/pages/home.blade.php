@extends('templates.master')

@section('main')
    <h2 class="mb-3 text-4xl font-bold leading-tight">Hallo 👋🏼,<br>I'm <x-link href="https://twitter.com/markvaneijk" rel="noopener" target="_blank">Mark.</x-link></h2>
    <p class="text-2xl leading-relaxed">
        I'm <x-link href="https://github.com/markvaneijk" rel="noopener" target="_blank">🧑🏻‍💻&nbsp;Full-Stack Maker of Webs</x-link>,
        <x-link href="https://vormkracht10.nl" rel="noopener" target="_blank">entrepeneur</x-link>,
        <x-link href="{{ route('post.index') }}">tinkerer</x-link> and
        <x-link href="{{ route('now') }}">currently working</x-link>
        on <x-link href="https://rocketee.rs">🚀&nbsp;Rocketeers</x-link> from <x-link href="https://www.nijmegen.nl">🇳🇱&nbsp;Nijmegen.</x-link>
</p>
    <div class="grid gap-4 mt-6 text-center md:grid-cols-3">
        <x-button href="mailto:m@rkvaneijk.nl">E-mail</x-button>
        <x-button href="https://twitter.com/markvaneijk">Twitter</x-button>
        <x-button href="https://linkedin.com/in/markveijk">LinkedIn</x-button>
    </div>
@endsection
