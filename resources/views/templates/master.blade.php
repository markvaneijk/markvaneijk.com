<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-slate-200">
    <head>
        @include('templates.partials.meta')
        @vite('resources/css/app.css')
        @if(app()->environment('production'))
        <script src="https://my.markvaneijk.com/script.js" data-site="EEMCYYWC" defer></script>
        @endif
    </head>
    <body class="bg-slate-200">
        <header class="p-6 mb-5 mb-10 md:p-10 bg-slate-100">
            <div class="container mx-auto md:max-w-lg">
                <h1 class="text-4xl">
                    @if(! request()->is('/'))<a href="/">@endif
                        Mark van Eijk
                    @if(! request()->is('/'))</a>@endif
                </h1>
            </div>
        </header>
        <main class="px-6 md:grid @yield('grid')">
            @yield('main')
        </main>
        <footer class="max-w-screen-xl p-6 mx-auto md:p-10">
            <div class="container pt-6 mx-auto border-t text-slate-600 border-slate-300">
                <p>This website is <a href="https://github.com/markvaneijk/markvaneijk.com" rel="noopener" target="_blank" class="underline underline-offset-4 decoration-4 decoration-yellow-400 hover:bg-yellow-100">open source</a></p>
                <p class="mb-2">Hosted by <a href="https://m.do.co/c/0801ad4bd810" target="_blank" rel="noopener" class="underline underline-offset-4 decoration-4 decoration-yellow-400 hover:bg-yellow-100">DigitalOcean</a> and analytics by <a href="https://usefathom.com/ref/WCCANW" target="_blank" rel="noopener" class="underline underline-offset-4 decoration-4 decoration-yellow-400 hover:bg-yellow-100">Fathom</a></p>
            </div>
        </footer>

        <script src="//instant.page/5.1.1" type="module" integrity="sha384-MWfCL6g1OTGsbSwfuMHc8+8J2u71/LA8dzlIN3ycajckxuZZmF+DNjdm7O6H3PSq"></script>
    </body>
</html>
