<?php

namespace Tests\Feature;

use Backstage\MinifyHtml\Middleware\MinifyHtml;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MinifyHtmlTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // The /now widgets are not what is under test here, and a page that
        // reaches for them is a page that waits on the network.
        Http::fake(['*' => Http::response(status: 403)]);
    }

    public function test_it_ships_pages_without_the_indentation(): void
    {
        $response = $this->get('/', ['Accept' => 'text/html']);

        $response->assertOk();
        $response->assertSee('<!DOCTYPE html><html', false);
        $this->assertStringNotContainsString(
            "\n        ",
            $this->betweenTags($response->getContent()),
            'Markup outside <script> and <pre> should carry no indentation.'
        );
    }

    /**
     * The inline scripts are the ones with something to lose: collapse their
     * newlines and the first `//` comment swallows the rest of the file, which
     * takes the theme with it. They must still parse.
     */
    public function test_it_leaves_the_inline_scripts_runnable(): void
    {
        $html = $this->get('/', ['Accept' => 'text/html'])->getContent();

        preg_match_all('~<script(?![^>]*\bsrc=)[^>]*>(.*?)</script>~s', $html, $matches);

        $this->assertNotEmpty($matches[1], 'The page should still carry its inline scripts.');

        foreach ($matches[1] as $script) {
            // Every line that opens a comment has to end at a newline, or
            // everything behind it on that line is commented out too.
            foreach (explode("\n", $script) as $line) {
                $this->assertStringNotContainsString(
                    '</',
                    (string) strstr($line, '//'),
                    'A line comment ran on into the markup behind it.'
                );
            }
        }

        // The structured data is JSON, not JavaScript, and rich results stop
        // at the first stray character.
        preg_match('~<script type="application/ld\+json">(.*?)</script>~s', $html, $jsonLd);

        $this->assertNotEmpty($jsonLd[1] ?? '', 'The page should still carry its JSON-LD.');
        $this->assertIsArray(json_decode($jsonLd[1], true), 'The JSON-LD no longer parses: '.json_last_error_msg());
    }

    /** The feed is XML by content type, which the middleware leaves alone. */
    public function test_it_leaves_the_feed_alone(): void
    {
        $response = $this->get('/feed', ['Accept' => 'text/html,application/xhtml+xml']);

        $response->assertOk();
        $response->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false);
        $this->assertNotNull(
            simplexml_load_string($response->getContent()),
            'The feed should still parse as XML.'
        );
    }

    public function test_it_leaves_a_page_alone_for_a_client_that_did_not_ask_for_html(): void
    {
        $minified = $this->get('/', ['Accept' => 'text/html'])->getContent();
        $untouched = $this->get('/', ['Accept' => 'application/json'])->getContent();

        $this->assertLessThan(
            strlen($untouched),
            strlen($minified),
            'Minifying should measurably shrink the page.'
        );
    }

    public function test_the_middleware_is_registered_on_the_web_group(): void
    {
        $this->assertContains(
            MinifyHtml::class,
            app('router')->getMiddlewareGroups()['web'],
        );
    }

    /** The markup with every <script> and <pre> body taken out of it. */
    private function betweenTags(string $html): string
    {
        return preg_replace('~<(script|pre|textarea)[^>]*>.*?</\1>~s', '', $html);
    }
}
