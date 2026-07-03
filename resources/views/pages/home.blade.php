@extends('templates.master')

@section('description', "Hi, I'm Mark van Eijk — a full-stack Laravel developer and entrepreneur from Nijmegen, the Netherlands. I build with Laravel, React, Inertia and Tailwind CSS.")

@section('grid') grid-cols-[minmax(20px,1fr)_minmax(360px,575px)_minmax(20px,1fr)] @endsection

@section('main')
    <div class="py-14 md:py-20 glow-field">
        <div class="flex flex-col-reverse items-start gap-8 mb-12 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="mb-3 text-sm text-term">~/nijmegen-nl $ whoami</p>
                <h1 class="text-5xl font-bold leading-none font-display md:text-6xl">Mark van Eijk<span class="text-flame">_</span></h1>
                <p class="mt-4 text-lg leading-relaxed text-muted">Full-stack Laravel developer &amp; entrepreneur from Nijmegen, the Netherlands.</p>
            </div>
            <img src="{{ asset('images/mark-van-eijk.png') }}" alt="Mark van Eijk" width="256" height="256" class="w-24 h-24 rounded-full md:w-32 md:h-32 border border-edge shadow-[0_0_45px_rgba(255,45,32,0.35)]">
        </div>

        <div class="term mb-12 text-[13px] md:text-sm">
            <div class="term-bar">
                <span class="term-dot bg-[#FF5F57]"></span>
                <span class="term-dot bg-[#FEBC2E]"></span>
                <span class="term-dot bg-[#28C840]"></span>
                <span class="ml-2 text-xs">mark@nijmegen — ~/markvaneijk.com</span>
            </div>
            <div class="term-body p-5 pb-6 space-y-1.5">
                <p class="mb-4"><span class="text-term">$</span> <span class="term-cmd">php artisan about</span><span class="term-cursor"></span></p>
                <p class="term-out term-row" style="--row:1"><span class="text-muted">Developer</span><span class="term-dots"></span><span>Mark van Eijk</span></p>
                <p class="term-out term-row" style="--row:2"><span class="text-muted">Location</span><span class="term-dots"></span><span>Nijmegen, NL 🇳🇱</span></p>
                <p class="term-out term-row" style="--row:3"><span class="text-muted">Role</span><span class="term-dots"></span><span>Full-Stack Maker of Webs</span></p>
                <p class="term-out term-row" style="--row:4"><span class="text-muted">Company</span><span class="term-dots"></span><x-link href="https://ux.nl">UX — ux.nl</x-link></p>
                <p class="term-out term-row" style="--row:5"><span class="text-muted">Building</span><span class="term-dots"></span><x-link href="https://rocketee.rs">Rocketeers 🚀</x-link></p>
                <p class="term-out term-row" style="--row:6"><span class="text-muted">Stack</span><span class="term-dots"></span><span><x-link href="https://laravel.com">Laravel</x-link> · <x-link href="https://reactjs.org">React</x-link> · <x-link href="https://inertiajs.com">Inertia</x-link> · <x-link href="https://tailwindcss.com">Tailwind</x-link></span></p>
                <p class="term-out term-row" style="--row:7"><span class="text-muted">Writing</span><span class="term-dots"></span><x-link href="{{ route('posts') }}">/posts</x-link></p>
                <p class="term-out term-row" style="--row:8"><span class="text-muted">Status</span><span class="term-dots"></span><span class="text-term"><span class="pulse">●</span> AVAILABLE FOR PROJECTS</span></p>
            </div>
        </div>

        <div class="grid gap-4 text-center md:grid-cols-4">
            <x-button href="mailto:m@rkvaneijk.nl">E-mail</x-button>
            <x-button href="https://github.com/markvaneijk">GitHub</x-button>
            <x-button href="https://x.com/markvaneijk">X</x-button>
            <x-button href="https://linkedin.com/in/markveijk">LinkedIn</x-button>
        </div>
    </div>
@endsection
