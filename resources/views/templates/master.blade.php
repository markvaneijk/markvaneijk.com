<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Mark van Eijk</title>
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        <script src="https://cdn.usefathom.com/script.js" data-site="EEMCYYWC" defer></script>
    </head>
    <body>
        <header>
            <div class="container mx-auto">
                @if(request()->is('/'))
                    <h1>Mark van Eijk</h1>
                @else
                    <h1><a href="/">Mark van Eijk</a></h1>
                @endif
            </div>
        </header>
        <main>
            <div class="container mx-auto">
                @yield('main')
            </div>
        </main>
        <footer>
            <div class="container mx-auto">
                <p>This website is <a href="https://github.com/markvaneijk/markvaneijk.com" rel="noopener" target="_blank">open source</a></p>
            </div>
        </footer>

        <script src="//instant.page/5.1.1" type="module" integrity="sha384-MWfCL6g1OTGsbSwfuMHc8+8J2u71/LA8dzlIN3ycajckxuZZmF+DNjdm7O6H3PSq"></script>
    </body>
</html>
