<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('templates.partials.meta')
        @stack('structured-data')
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=chakra-petch:600,700|jetbrains-mono:400,500,700&display=swap" rel="stylesheet">
        @vite('resources/css/app.css')
        @env('production')
        <script src="https://cdn.visitors.now/v.js" data-token="ccbd95ad-850a-4f12-9d3b-6cfdb98c6b96" defer></script>
        @endenv
    </head>
    <body>
        <header class="px-6 py-5 border-b md:px-10 border-edge">
            <div class="container flex items-baseline justify-between mx-auto md:max-w-xl">
                <a href="/" class="font-bold"><span class="text-term">$</span> mark-van-eijk<span class="text-flame">_</span></a>
                <nav class="flex gap-2 text-sm text-muted">
                    <a href="{{ route('posts') }}" class="px-2 py-3 -my-3 transition-colors hover:text-term">/posts</a>
                    <a href="{{ route('now') }}" class="px-2 py-3 -my-3 transition-colors hover:text-term">/now</a>
                </nav>
            </div>
        </header>
        <main class="px-6 md:grid @yield('grid')">
            @yield('main')
        </main>
        <footer class="max-w-screen-xl p-6 mx-auto md:p-10">
            <div class="container pt-6 mx-auto space-y-1 text-sm border-t border-edge text-muted">
                <p><span class="text-term">#</span> Made in Nijmegen — the oldest city in the Netherlands, est. 98 AD.</p>
                <p><span class="text-term">#</span> This website is <a href="https://github.com/markvaneijk/markvaneijk.com" rel="noopener" target="_blank" class="t-link">open source</a>.</p>
            </div>
        </footer>

        <script src="//instant.page/5.1.1" type="module" integrity="sha384-MWfCL6g1OTGsbSwfuMHc8+8J2u71/LA8dzlIN3ycajckxuZZmF+DNjdm7O6H3PSq"></script>
    </body>
</html>
