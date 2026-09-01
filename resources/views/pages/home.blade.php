@extends('templates.master')

@section('description', __('site.home.description'))

@section('grid') grid-cols-[minmax(20px,1fr)_minmax(360px,575px)_minmax(20px,1fr)] @endsection

@section('main')
    <div class="py-14 md:py-20 glow-field">
        <div class="flex flex-col-reverse items-start gap-8 mb-12 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="mb-3 text-sm text-term">~/nijmegen-nl $ whoami</p>
                <h1 class="text-5xl font-bold leading-none font-display md:text-6xl">Mark van Eijk<span class="text-flame">_</span></h1>
                <p class="mt-4 text-lg leading-relaxed text-muted">{{ __('site.home.tagline') }}</p>
            </div>
            <img src="{{ asset('images/mark-van-eijk.png') }}" alt="Mark van Eijk" width="256" height="256" class="w-24 h-24 rounded-full md:w-32 md:h-32 border-8 border-[var(--avatar-ring)] shadow-[0_0_45px_var(--glow-flame)]">
        </div>

        <section class="mb-12">
            <p class="mb-3 text-sm text-term">~ $ cat about.md</p>
            <h2 class="sr-only">{{ __('site.home.about_heading') }}</h2>
            {{-- The links sit in the middle of these sentences and no two
                 languages put them in the same place, so they travel with the
                 copy in lang/*/site.php rather than standing here. --}}
            <div class="space-y-4 leading-relaxed text-muted">
                <p>{!! __('site.home.about.stack') !!}</p>
                <p>{!! __('site.home.about.work') !!}</p>
                <p>{!! __('site.home.about.writing', ['posts' => route('posts')]) !!}</p>
            </div>
        </section>

        <div class="term mb-12 text-[13px] md:text-sm">
            <div class="term-bar">
                <span class="term-dot bg-[#FF5F57]"></span>
                <span class="term-dot bg-[#FEBC2E]"></span>
                <span class="term-dot bg-[#28C840]"></span>
                {{-- The site this page is part of, not the host it is being
                     served from: a `.test` or a preview domain in the window
                     title says nothing to whoever reads it. --}}
                <span class="ml-2 text-xs">mark@nijmegen — ~/{{ \App\Support\Locales::domain() }}</span>
            </div>
            <div class="term-body p-5 pb-6 space-y-1.5">
                <p class="mb-4"><span class="text-term">$</span> <span class="term-cmd">php artisan about</span><span class="term-cursor"></span></p>
                <p class="term-out term-row" style="--row:1"><span class="text-muted">{{ __('site.home.about_command.developer') }}</span><span class="term-dots"></span><span>Mark van Eijk</span></p>
                <p class="term-out term-row" style="--row:2"><span class="text-muted">{{ __('site.home.about_command.location') }}</span><span class="term-dots"></span><span>Nijmegen, NL 🇳🇱</span></p>
                <p class="term-out term-row" style="--row:3"><span class="text-muted">{{ __('site.home.about_command.role') }}</span><span class="term-dots"></span><span>{{ __('site.home.role') }}</span></p>
                <p class="term-out term-row" style="--row:4"><span class="text-muted">{{ __('site.home.about_command.company') }}</span><span class="term-dots"></span><x-link href="https://ux.nl">UX — ux.nl</x-link></p>
                <p class="term-out term-row" style="--row:5"><span class="text-muted">{{ __('site.home.about_command.building') }}</span><span class="term-dots"></span><x-link href="https://rocketee.rs">Rocketeers 🚀</x-link></p>
                <p class="term-out term-row" style="--row:6"><span class="text-muted">{{ __('site.home.about_command.stack') }}</span><span class="term-dots"></span><span><x-link href="https://laravel.com">Laravel</x-link> · <x-link href="https://reactjs.org">React</x-link> · <x-link href="https://inertiajs.com">Inertia</x-link> · <x-link href="https://tailwindcss.com">Tailwind</x-link></span></p>
                <p class="term-out term-row" style="--row:7"><span class="text-muted">{{ __('site.home.about_command.writing') }}</span><span class="term-dots"></span><x-link href="{{ route('posts') }}">/posts</x-link></p>
                <p class="term-out term-row" style="--row:8"><span class="text-muted">{{ __('site.home.about_command.status') }}</span><span class="term-dots"></span><span class="text-term"><span class="pulse">●</span> {{ __('site.home.available') }}</span></p>
            </div>
        </div>

        <div class="grid gap-4 text-center md:grid-cols-4">
            <x-button href="mailto:m@rkvaneijk.nl">{{ __('site.home.email') }}</x-button>
            <x-button href="https://github.com/markvaneijk">GitHub</x-button>
            <x-button href="https://x.com/markvaneijk">X</x-button>
            <x-button href="https://linkedin.com/in/markveijk">LinkedIn</x-button>
        </div>
    </div>
@endsection
