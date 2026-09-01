# Personal website of Mark van Eijk

## Installation

```bash
composer install
pnpm install # or npm install
npx vite
```

## Two domains, two languages

markvaneijk.com is the English site and markvaneijk.nl the Dutch one. One
application, the same routes and the same paths on both:
`App\Http\Middleware\SetLocaleFromHost` reads the host the request arrived on,
and that is the whole of the difference. Which domain is which language is
`config/locales.php`; a host that is neither — a preview domain, `localhost`,
the console — is served in the first language listed there.

Every word lives in `lang/en/site.php` and `lang/nl/site.php`. A key the Dutch
file does not have falls back to the English one, so a new page is never blank
in one language: it is English until it is translated.

Each page names its other half. hreflang for both domains plus an x-default,
`og:locale` with the alternate beside it, and a canonical that stays on the
domain that was asked for. Numbers follow the language too — 1.234 and 15,3
where English writes 1,234 and 15.3 — which is what `App\Support\Number` is for,
and `robots.txt` names both sitemaps because it is one file served on both.

Two things follow from the caching. The /now widgets keep the HTML they were
drawn as, so the language is part of the key they keep it under: every widget is
registered once per language in `AppServiceProvider`, and the scheduler keeps
both warm. And the static cache already keeps a file per domain
(`static.files.include_domain`), so the two sites never hand each other a page.

Locally the domain has to exist before it can pick anything, so the Dutch site
needs a second name for this same directory:

```bash
valet link markvaneijk.nl    # markvaneijk.nl.test, beside markvaneijk.com.test
```

A third language is `config/locales.php`, a `lang/<code>/site.php`, and a domain
pointed at this same application.

## The /now widgets

`/now` reads five APIs — Spotify, Last.fm, GitHub, Strava and X — and used to
read them in front of whoever asked for the page first. Each widget is now a
permanently cached Blade component through
[backstage/laravel-permanent-cache](https://github.com/backstagephp/laravel-permanent-cache):
the HTML it renders is kept in the `permanent` cache store and handed out as it
stands, and a scheduled run in the background is the only thing that ever waits
on an API. So the first hit after a deploy costs a render rather than five round
trips.

That makes the scheduler load-bearing. Without it the widgets keep whatever they
were last drawn with — including nothing at all, if an API was down the one time
they were drawn — so the machine serving the site needs the usual entry:

```cron
* * * * * cd /path/to/site && php artisan schedule:run >> /dev/null 2>&1
```

And after a deploy, before the first visitor arrives:

```bash
php artisan permanent-cache:update    # draw every widget once, in the background
php artisan permanent-cache:status    # what is cached, how big, how long ago, how often
```

The store is deliberately not the default one: `cache:clear` empties that, and an
empty permanent cache is exactly the wait this removes. `cache:clear permanent`
is how you empty this one on purpose.

Adding a widget, or drawing an existing one with different arguments, means
registering that combination in `AppServiceProvider` — the arguments are part of
the cache key, and an unregistered combination has no background run behind it.
The language is one of those arguments, so a widget is two registrations.
`NowWidgetsTest` holds `resources/views/pages/now.blade.php` and that list
against each other.

## Open Graph images

Every page links a share image that is drawn for that page — the terminal card
in `resources/views/vendor/og-image/template.blade.php`, rendered by headless
Chrome through [backstage/laravel-og-image](https://github.com/backstagephp/laravel-og-image)
and cached in `storage/app/public/og-images`.

That needs two things on the machine serving the site:

```bash
brew install chromium                 # or Google Chrome; CHROME_PATH points at the binary
php artisan storage:link              # the cached images are served from public/storage
```

While restyling the card, set `OG_IMAGE_DEBUG=true` to re-render on every
request, and open `/og-image/preview?title=…` to get the HTML instead of the
image. Afterwards, `php artisan og-image:clear-cache` drops what was cached.
