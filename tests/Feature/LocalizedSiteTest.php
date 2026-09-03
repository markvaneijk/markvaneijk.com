<?php

namespace Tests\Feature;

use App\Domain\Socials\Store;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Two domains, one application: markvaneijk.nl is the Dutch site and
 * markvaneijk.com the English one. Same routes, same posts, same widgets — the
 * host a request arrives on is the whole of the difference.
 *
 * /now is the only page here that reaches for an API, and the one test that
 * asks for it brings its own stubs: Http::fake() adds to what is already
 * stubbed and the first match answers, so a catch-all in setUp would be the
 * one answering every call.
 */
class LocalizedSiteTest extends TestCase
{
    public function test_the_dutch_domain_serves_the_page_in_dutch(): void
    {
        $response = $this->get('https://markvaneijk.nl/');

        $response->assertOk();
        $response->assertSee('<html lang="nl">', false);
        $response->assertSee('Full-stack Laravel-developer &amp; ondernemer uit Nijmegen.', false);
        $response->assertSee('BESCHIKBAAR VOOR PROJECTEN');
        $response->assertSee('de oudste stad van Nederland');
        $response->assertDontSee('AVAILABLE FOR PROJECTS');
    }

    public function test_the_com_domain_serves_the_page_in_english(): void
    {
        $response = $this->get('https://markvaneijk.com/');

        $response->assertOk();
        $response->assertSee('<html lang="en">', false);
        $response->assertSee('Full-stack Laravel developer &amp; entrepreneur from Nijmegen, the Netherlands.', false);
        $response->assertSee('AVAILABLE FOR PROJECTS');
        $response->assertDontSee('BESCHIKBAAR VOOR PROJECTEN');
    }

    /**
     * The projects are one list in one place, with only the line under each
     * name translated — and Piggie, which has no site, is the one that must
     * come out as a name and nothing more.
     */
    public function test_the_projects_are_listed_in_the_language_of_the_page(): void
    {
        $dutch = $this->get('https://markvaneijk.nl/');

        $dutch->assertSee('~ $ ls ~/projects');
        $dutch->assertSee('Projecten<span class="text-flame">_</span>', false);
        $dutch->assertSee('https://backstagephp.com');
        $dutch->assertSee('Open source packages voor Laravel en premium plugins voor Filament.');
        $dutch->assertSee('Piggie');
        $dutch->assertDontSee('https://piggie');

        $english = $this->get('https://markvaneijk.com/');

        $english->assertSee('Projects<span class="text-flame">_</span>', false);
        $english->assertSee('Open source packages for Laravel and premium plugins for Filament.');
        $english->assertDontSee('Open source packages voor Laravel');
    }

    /**
     * A preview domain, an IP address, `localhost` — anything that is not one
     * of the two sites is served in the language the site started out in.
     */
    public function test_a_host_that_is_neither_domain_falls_back_to_english(): void
    {
        $this->get('http://localhost/')->assertOk()->assertSee('AVAILABLE FOR PROJECTS');
    }

    /** The local and staging hosts carry the domain with something after it. */
    public function test_a_local_or_prefixed_host_is_served_in_its_own_language(): void
    {
        $this->get('http://markvaneijk.nl.test/')->assertOk()->assertSee('BESCHIKBAAR VOOR PROJECTEN');
        $this->get('https://www.markvaneijk.nl/')->assertOk()->assertSee('BESCHIKBAAR VOOR PROJECTEN');
        $this->get('http://markvaneijk.com.test/')->assertOk()->assertSee('AVAILABLE FOR PROJECTS');
    }

    /**
     * The two sites are translations of one another, and a search engine is
     * only told so if every page says where its other language lives — under
     * the same path, since the routes are the same on both.
     */
    public function test_every_page_points_at_itself_on_the_other_domain(): void
    {
        $response = $this->get('https://markvaneijk.nl/posts');

        $response->assertOk();
        $response->assertSee('<link rel="canonical" href="https://markvaneijk.nl/posts">', false);
        $response->assertSee('<link rel="alternate" hreflang="nl" href="https://markvaneijk.nl/posts">', false);
        $response->assertSee('<link rel="alternate" hreflang="en" href="https://markvaneijk.com/posts">', false);
        $response->assertSee('<link rel="alternate" hreflang="x-default" href="https://markvaneijk.com/posts">', false);
    }

    /** The home page is the one whose path is not a path. */
    public function test_the_home_page_alternates_carry_no_trailing_slash(): void
    {
        $response = $this->get('https://markvaneijk.com/');

        $response->assertSee('<link rel="alternate" hreflang="nl" href="https://markvaneijk.nl">', false);
        $response->assertSee('<link rel="alternate" hreflang="en" href="https://markvaneijk.com">', false);
    }

    public function test_the_share_card_is_told_which_language_the_page_is_in(): void
    {
        $this->get('https://markvaneijk.nl/')
            ->assertSee('<meta property="og:locale" content="nl_NL">', false)
            ->assertSee('<meta property="og:locale:alternate" content="en_US">', false);

        $this->get('https://markvaneijk.com/')
            ->assertSee('<meta property="og:locale" content="en_US">', false)
            ->assertSee('<meta property="og:locale:alternate" content="nl_NL">', false);
    }

    /** Rich results are read in the language of the page that carries them. */
    public function test_the_structured_data_speaks_the_language_of_the_page(): void
    {
        $this->get('https://markvaneijk.nl/')->assertSee('"jobTitle": "Laravel-developer"', false);
        $this->get('https://markvaneijk.com/')->assertSee('"jobTitle": "Laravel Developer"', false);
    }

    public function test_the_feed_declares_the_language_it_is_written_in(): void
    {
        $this->get('https://markvaneijk.nl/feed')
            ->assertOk()
            ->assertSee('<language>nl</language>', false)
            ->assertSee('Berichten van Mark van Eijk');

        $this->get('https://markvaneijk.com/feed')
            ->assertOk()
            ->assertSee('<language>en</language>', false)
            ->assertSee('Posts by Mark van Eijk');
    }

    /**
     * route() writes the host that asked, so a link home on the Dutch site
     * points at markvaneijk.nl — and must still count as this site's own,
     * rather than being handed a tab of its own like an outside link.
     */
    public function test_a_link_to_this_site_stays_in_the_tab_it_was_opened_in(): void
    {
        $this->get('https://markvaneijk.nl/')
            ->assertSee('<a href="https://markvaneijk.nl/posts" class="t-link">/posts</a>', false);
    }

    /**
     * The widgets are drawn once and then handed out as they stand, so the
     * language they were drawn in has to be part of what they are kept under —
     * or whichever domain was asked first would answer for both.
     */
    public function test_the_now_widgets_are_kept_per_language(): void
    {
        Http::fake([
            'api.x.com/*' => Http::response(['data' => ['public_metrics' => ['followers_count' => 1234]]]),
            'www.strava.com/api/v3/athlete/activities*' => Http::response([['distance' => 10000.0], ['distance' => 5250.0]]),
            '*' => Http::response(status: 403),
        ]);

        config([
            'services.x.username' => 'markvaneijk',
            'services.x.client_id' => 'x-id',
            'services.x.client_secret' => 'x-secret',
            'services.strava.client_id' => 1,
            'services.strava.client_secret' => 'strava-secret',
        ]);

        Store::make()->put('x.access_token', 'x-token', 3600);
        Store::make()->put('strava.access_token', 'strava-token', 3600);

        $dutch = $this->get('https://markvaneijk.nl/now');

        $dutch->assertOk();
        $dutch->assertSee('Nu<span class="text-flame">_</span>', false);
        $dutch->assertSee('Volgers');
        $dutch->assertSee('Afstand · laatste 30 dagen', false);
        $dutch->assertSee('Afstand · laatste 12 maanden', false);
        // Dutch writes 1.234 and 15,3 where English writes 1,234 and 15.3.
        $dutch->assertSee('1.234');
        $dutch->assertSee('15,3');
        $dutch->assertDontSee('Followers');

        $english = $this->get('https://markvaneijk.com/now');

        $english->assertOk();
        $english->assertSee('Followers');
        $english->assertSee('Distance · last 30 days', false);
        $english->assertSee('1,234');
        $english->assertSee('15.3');
        $english->assertDontSee('Volgers');
    }
}
