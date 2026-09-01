<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;

/**
 * The two sites this application serves: markvaneijk.com in English and
 * markvaneijk.nl in Dutch. Same routes, same data, same pages — the host is
 * what says which language they are written in.
 *
 * @see config/locales.php for the map itself.
 */
class Locales
{
    /**
     * @return array<string, array{domain: string, og: string}>
     */
    public static function all(): array
    {
        return config('locales');
    }

    /**
     * The language everything that is not one of the domains is served in.
     */
    public static function default(): string
    {
        return array_key_first(static::all());
    }

    /**
     * The language a host is served in. Matched anywhere in the host rather
     * than pinned to its end, so `www.markvaneijk.nl` and the local
     * `markvaneijk.nl.test` land on the same language as the domain itself.
     */
    public static function forHost(string $host): string
    {
        foreach (static::all() as $locale => $site) {
            if (str_contains($host, $site['domain'])) {
                return $locale;
            }
        }

        return static::default();
    }

    /**
     * The domain a language is served on, whatever host is asking right now —
     * so a page can name the site it is part of without printing back the
     * `.test` or preview host it happens to be rendered on.
     */
    public static function domain(?string $locale = null): string
    {
        $locale ??= App::getLocale();

        return (static::all()[$locale] ?? static::all()[static::default()])['domain'];
    }

    /**
     * Make everything that reads a language read this one — including Carbon,
     * which the /now widgets ask for their "20 minutes ago" and which does not
     * follow app()->setLocale() on its own.
     */
    public static function apply(string $locale): void
    {
        App::setLocale($locale);

        Carbon::setLocale($locale);
    }

    /**
     * The Open Graph spelling of a locale.
     */
    public static function openGraph(string $locale): string
    {
        return static::all()[$locale]['og'] ?? static::all()[static::default()]['og'];
    }

    /**
     * The same page on every language's own domain, for the hreflang tags.
     * Always the canonical domains and always https, whatever host the page is
     * being served on right now: a `.test` or a preview host in a search
     * engine's index is worse than no alternate at all.
     *
     * @return array<string, string>
     */
    public static function alternates(string $path): array
    {
        return array_map(
            fn (array $site) => rtrim('https://'.$site['domain'].'/'.ltrim($path, '/'), '/'),
            static::all(),
        );
    }
}
