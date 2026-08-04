<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('templates.partials.meta')
        {{-- Runs before first paint — and after the meta tags, so it can colour
             the browser chrome to match the theme it picks. --}}
        <script>
            (function () {
                var chromeColors = { dark: '#0B0E14', light: '#F4F1EA' };
                var meta = document.querySelector('meta[name="theme-color"]');
                var system = window.matchMedia('(prefers-color-scheme: light)');

                function stored() {
                    try {
                        return localStorage.getItem('theme');
                    } catch (e) {
                        return null;
                    }
                }

                window.applyTheme = function (theme) {
                    document.documentElement.setAttribute('data-theme', theme);

                    if (meta) {
                        meta.setAttribute('content', chromeColors[theme]);
                    }

                    var toggle = document.getElementById('theme-toggle');

                    if (toggle) {
                        toggle.setAttribute('aria-label', theme === 'light' ? 'Switch to dark theme' : 'Switch to light theme');
                    }
                };

                applyTheme(stored() || (system.matches ? 'light' : 'dark'));

                // Keep following the OS until the visitor picks a theme themselves.
                system.addEventListener('change', function (event) {
                    if (! stored()) {
                        applyTheme(event.matches ? 'light' : 'dark');
                    }
                });
            })();
        </script>
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
                <nav class="flex items-center gap-2 text-sm text-muted">
                    <a href="{{ route('posts') }}" class="px-2 py-3 -my-3 transition-colors hover:text-term">/posts</a>
                    <a href="{{ route('now') }}" class="px-2 py-3 -my-3 transition-colors hover:text-term">/now</a>
                    <button type="button" id="theme-toggle" aria-label="Switch to light theme" title="Toggle theme" class="flex px-2 py-3 -my-3 transition-colors cursor-pointer hover:text-term">
                        <svg class="icon-sun size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="4.25"/>
                            <path d="M12 2.5v2.2M12 19.3v2.2M4.28 4.28l1.56 1.56M18.16 18.16l1.56 1.56M2.5 12h2.2M19.3 12h2.2M4.28 19.72l1.56-1.56M18.16 5.84l1.56-1.56"/>
                        </svg>
                        <svg class="icon-moon size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20.5 14.6A8.6 8.6 0 0 1 9.4 3.5a8.6 8.6 0 1 0 11.1 11.1Z"/>
                        </svg>
                    </button>
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

        <script>
            (function () {
                var root = document.documentElement;
                var toggle = document.getElementById('theme-toggle');

                if (! toggle || ! window.applyTheme) {
                    return;
                }

                // The head script ran before this button existed; re-apply so it
                // gets the label for the theme already on screen.
                applyTheme(root.getAttribute('data-theme'));

                toggle.addEventListener('click', function () {
                    var next = root.getAttribute('data-theme') === 'light' ? 'dark' : 'light';

                    root.classList.add('theme-transition');
                    applyTheme(next);

                    try {
                        localStorage.setItem('theme', next);
                    } catch (e) {}

                    window.setTimeout(function () {
                        root.classList.remove('theme-transition');
                    }, 250);
                });
            })();
        </script>

        <script src="//instant.page/5.1.1" type="module" integrity="sha384-MWfCL6g1OTGsbSwfuMHc8+8J2u71/LA8dzlIN3ycajckxuZZmF+DNjdm7O6H3PSq"></script>
    </body>
</html>
