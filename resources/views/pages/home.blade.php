@extends('templates.master')

@section('grid') grid-cols-[minmax(20px,1fr)_minmax(360px,500px)_minmax(20px,1fr)] @endsection

@section('main')
    <div class="md:py-20">
        <h2 class="mb-3 text-4xl font-bold leading-tight">Hallo 👋🏼,<br>I'm <x-link href="https://twitter.com/markvaneijk">Mark.</x-link></h2>
        <p class="mb-6 text-2xl leading-relaxed">
            I'm <x-link href="https://github.com/markvaneijk">🧑🏻‍💻&nbsp;Full-Stack Maker of Webs,</x-link>
            <x-link href="https://vormkracht10.nl">entrepeneur,</x-link>
            <x-link href="{{ route('posts') }}">tinkerer</x-link> and
            <x-link href="{{ route('now') }}">currently working</x-link>
            on <x-link href="https://rocketee.rs">🚀&nbsp;Rocketeers</x-link> and I live in <x-link href="https://www.nijmegen.nl">🇳🇱&nbsp;Nijmegen.</x-link>
        </p>
        <p class="mb-6 text-2xl leading-relaxed">
            I 💛 working with <x-link href="https://laravel.com" class="font-medium">#Laravel,</x-link>
            <x-link href="https://reactjs.org" class="font-medium">#React,</x-link>
            <x-link href="https://inertiajs.com" class="font-medium">#Inertia</x-link> and
            <x-link href="https://tailwindcss.com" class="font-medium">#Tailwindcss.</x-link>
        </p>
        <div class="grid gap-4 text-center md:grid-cols-3">
            <x-button href="mailto:m@rkvaneijk.nl">E-mail</x-button>
            <x-button href="https://twitter.com/markvaneijk">Twitter</x-button>
            <x-button href="https://linkedin.com/in/markveijk">LinkedIn</x-button>
        </div>
    </div>
@endsection
