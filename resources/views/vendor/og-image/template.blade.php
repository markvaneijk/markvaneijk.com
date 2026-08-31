{{-- The card behind every og:image on this site, rendered by headless Chrome at
     1200x630. It mirrors the terminal window on the home page, so a shared link
     looks like the site it points at.

     Every variable arrives from the query string of the signed og-image URL that
     templates/partials/meta.blade.php builds, so each one gets a fallback: the
     local /og-image/preview route can be opened with any subset of them. --}}
@php($title = trim((string) ($title ?? '')) ?: config('app.name'))
@php($description = trim((string) ($description ?? '')))
@php($path = '/'.ltrim((string) ($path ?? '/'), '/'))
@php($date = trim((string) ($date ?? '')))

{{-- The home page runs `whoami`; everything else reads the page it links to. --}}
@php($command = $path === '/' ? 'whoami' : 'cat '.trim($path, '/'))

{{-- Inlined rather than linked: Chrome fails the screenshot on an image it
     cannot load, and a data URI cannot miss. --}}
@php($avatar = is_file(public_path('images/mark-van-eijk.png')) ? 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('images/mark-van-eijk.png'))) : null)
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=chakra-petch:700|jetbrains-mono:400,500,700&display=swap" rel="stylesheet">
        <style>
            :root {
                --void: #0B0E14;
                --panel: #10141D;
                --edge: #232A38;
                --flame: #FF2D20;
                --term: #3FDD78;
                --fg: #E6EAF2;
                --muted: #7A8494;
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                width: 1200px;
                height: 630px;
                display: flex;
                padding: 56px;
                background-color: var(--void);
                /* The same 36px grid and red/green bloom as the site itself. */
                background-image:
                    radial-gradient(620px 380px at 20% 0%, rgba(255, 45, 32, 0.22), transparent 70%),
                    radial-gradient(520px 340px at 90% 10%, rgba(63, 221, 120, 0.12), transparent 70%),
                    linear-gradient(rgba(230, 234, 242, 0.035) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(230, 234, 242, 0.035) 1px, transparent 1px);
                background-size: auto, auto, 36px 36px, 36px 36px;
                color: var(--fg);
                font-family: "JetBrains Mono", ui-monospace, monospace;
                overflow: hidden;
            }

            .term {
                flex: 1;
                display: flex;
                flex-direction: column;
                background: var(--panel);
                border: 1px solid var(--edge);
                border-radius: 12px;
                box-shadow:
                    0 0 80px rgba(255, 45, 32, 0.14),
                    0 24px 70px rgba(0, 0, 0, 0.5);
                overflow: hidden;
            }

            .term-bar {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 16px 22px;
                border-bottom: 1px solid var(--edge);
                color: var(--muted);
                font-size: 18px;
            }

            .term-dot {
                width: 14px;
                height: 14px;
                border-radius: 50%;
            }

            .term-bar span:last-child {
                margin-left: 8px;
            }

            .term-body {
                flex: 1;
                display: flex;
                flex-direction: column;
                padding: 44px 48px 40px;
            }

            .prompt {
                margin-bottom: 22px;
                color: var(--term);
                font-size: 24px;
            }

            h1 {
                font-family: "Chakra Petch", "JetBrains Mono", sans-serif;
                font-size: 68px;
                font-weight: 700;
                line-height: 1.06;
                letter-spacing: -0.5px;
                /* Long titles get cut off rather than pushing the footer out of
                   the 630px the card is screenshotted at. */
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            h1 .cursor {
                color: var(--flame);
            }

            p.description {
                margin-top: 24px;
                color: var(--muted);
                font-size: 26px;
                line-height: 1.5;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            footer {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 24px;
                margin-top: auto;
                padding-top: 32px;
                font-size: 22px;
            }

            footer .site {
                font-weight: 700;
            }

            footer .site .sigil {
                color: var(--term);
            }

            footer .site .cursor {
                color: var(--flame);
            }

            footer .meta {
                display: flex;
                align-items: center;
                gap: 20px;
                color: var(--muted);
            }

            footer img {
                width: 76px;
                height: 76px;
                border-radius: 50%;
                border: 4px solid var(--edge);
                box-shadow: 0 0 40px rgba(255, 45, 32, 0.35);
            }
        </style>
    </head>
    <body>
        <div class="term">
            <div class="term-bar">
                <span class="term-dot" style="background: #FF5F57"></span>
                <span class="term-dot" style="background: #FEBC2E"></span>
                <span class="term-dot" style="background: #28C840"></span>
                <span>mark@nijmegen — ~/markvaneijk.com</span>
            </div>
            <div class="term-body">
                <p class="prompt">~ $ {{ $command }}</p>
                <h1>{{ $title }}<span class="cursor">_</span></h1>
                @if($description !== '')
                    <p class="description">{{ $description }}</p>
                @endif
                <footer>
                    <p class="site"><span class="sigil">$</span> mark-van-eijk<span class="cursor">_</span></p>
                    <div class="meta">
                        @if($date !== '')
                            <span>{{ $date }}</span>
                        @endif
                        @if($avatar)
                            <img src="{{ $avatar }}" alt="Mark van Eijk">
                        @endif
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>
