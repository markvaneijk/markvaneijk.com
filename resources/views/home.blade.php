<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Mark van Eijk</title>
        <style>
        {!! file_get_contents(public_path('css/critical.css')) !!}
        </style>
    </head>
    <body>
        <header>
            <div class="container mx-auto">
                <h1>Mark van Eijk</h1>
            </div>
        </header>
        <main>
            <div class="container mx-auto">
            </div>
        </main>
        <footer>
            <div class="container mx-auto">
                <p>This website is <a href="https://github.com/markvaneijk/markvaneijk.com" rel="noopener" target="_blank">open source</a></p>
            </div>
        </footer>

        <script src="//instant.page/3.0.0" type="module" integrity="sha384-OeDn4XE77tdHo8pGtE1apMPmAipjoxUQ++eeJa6EtJCfHlvijigWiJpD7VDPWXV1" defer></script>
    </body>
</html>
