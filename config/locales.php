<?php

return [
    /**
     * The languages this site is served in, and the domain each one belongs to.
     * The host a request arrives on is the only thing that picks a language —
     * there is no switch, no cookie and no Accept-Language — so this map is
     * read both ways: App\Support\Locales turns a host into a locale, and turns
     * every locale back into the URL of the page being served on its own
     * domain, which is what the hreflang tags are made of.
     *
     * The first entry is the default: a host that matches none of them — a
     * preview domain, an IP address, the console — is served in it, and it is
     * the hreflang x-default.
     *
     * `og` is the Open Graph spelling of the same language; it wants a locale
     * with a territory, which hreflang and <html lang> deliberately leave out.
     */
    'en' => [
        'domain' => 'markvaneijk.com',
        'og' => 'en_US',
    ],

    'nl' => [
        'domain' => 'markvaneijk.nl',
        'og' => 'nl_NL',
    ],
];
