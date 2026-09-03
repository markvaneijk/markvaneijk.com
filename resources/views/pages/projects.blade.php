@extends('templates.master')

@section('title', __('site.projects.title'))
@section('description', __('site.projects.description'))
@section('grid') grid-cols-[minmax(20px,1fr)_minmax(360px,575px)_minmax(20px,1fr)] @endsection

@section('main')
    <div class="py-14 md:py-20">
        <p class="mb-3 text-sm text-term">~ $ ls ~/projects</p>
        <h1 class="mb-4 text-5xl font-bold leading-none font-display">{{ __('site.projects.title') }}<span class="text-flame">_</span></h1>
        <p class="mb-10 text-lg leading-relaxed text-muted">{{ __('site.projects.intro') }}</p>

        {{-- A name and a domain read the same in either language, so only the
             line underneath travels with the copy in lang/*/site.php. Piggie
             has nothing to point at, and gets the same card without a link
             rather than a link that goes nowhere. --}}
        @php($projects = [
            ['key' => 'ux', 'name' => 'UX', 'url' => 'https://ux.nl'],
            ['key' => 'backstage', 'name' => 'Backstage PHP', 'url' => 'https://backstagephp.com'],
            ['key' => 'teamflow', 'name' => 'TeamFlow', 'url' => 'https://goteamflow.com'],
            ['key' => 'rocketeers', 'name' => 'Rocketeers', 'url' => 'https://rocketeersapp.com'],
            ['key' => 'zorgformulieren', 'name' => 'Zorgformulieren', 'url' => 'https://zorgformulieren.nl'],
            ['key' => 'artsenverklaringen', 'name' => 'Artsenverklaringen', 'url' => 'https://artsenverklaringen.nl'],
            ['key' => 'piggie', 'name' => 'Piggie', 'url' => null],
        ])

        <ul class="grid gap-3 sm:grid-cols-2">
            @foreach($projects as $project)
                {{-- One link per card, stretched over the whole `li`: the name
                     carries it, so the card keeps a single accessible name and
                     the line under it is part of the same hit area. --}}
                <li @class([
                    'relative p-4 transition-colors border rounded-md border-edge bg-panel',
                    'group hover:border-flame' => $project['url'],
                ])>
                    <p class="font-bold">
                        @if($project['url'])
                            <a href="{{ $project['url'] }}" rel="noopener" target="_blank"
                                class="t-link after:absolute after:inset-0 after:rounded-md focus-visible:outline-none focus-visible:after:outline-2 focus-visible:after:outline-term focus-visible:after:outline-offset-2">{{ $project['name'] }}</a>
                        @else
                            {{ $project['name'] }}
                        @endif
                    </p>
                    <p class="mt-1 text-sm leading-relaxed text-muted">{{ __('site.projects.'.$project['key']) }}</p>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
