# Personal website of Mark van Eijk

## Installation

```bash
composer install
pnpm install # or npm install
npx vite
```

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
