<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class OgImageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Nothing here is about the /now widgets, and a page that reaches for
        // them is a page that waits on the network.
        Http::fake(['*' => Http::response(status: 403)]);
    }

    public function test_it_links_a_card_drawn_for_the_page(): void
    {
        $parameters = $this->cardParameters($this->get('/')->getContent());

        $this->assertSame('Mark van Eijk', $parameters['title']);
        $this->assertSame('/', $parameters['path']);
        $this->assertStringStartsWith("Hi, I'm Mark van Eijk — a full-stack Laravel developer", $parameters['description']);
        $this->assertArrayHasKey('signature', $parameters, 'The image URL should be signed; the route turns away anything else.');
    }

    /** The image is cached under the signature, so the URL has to survive a reload. */
    public function test_the_same_page_asks_for_the_same_image(): void
    {
        $this->assertSame(
            $this->cardUrl($this->get('/posts')->getContent()),
            $this->cardUrl($this->get('/posts')->getContent()),
        );
    }

    public function test_the_card_carries_the_title_and_the_date_of_a_post(): void
    {
        $post = (new Post)->forceFill([
            'title' => 'Caching routes using filters in Laravel',
            'slug' => 'caching-routes-using-filters-in-laravel',
            'content' => 'Filters run before the route does, which is early enough to answer from cache.',
            'published_at' => '2014-02-11',
        ]);

        $parameters = $this->cardParameters(View::make('posts.show', ['post' => $post])->render());

        $this->assertSame($post->title, $parameters['title']);
        $this->assertSame($post->excerpt, $parameters['description']);
        $this->assertSame('2014-02-11', $parameters['date']);
    }

    public function test_it_declares_the_size_of_the_card_and_asks_for_the_large_one(): void
    {
        $response = $this->get('/');

        $response->assertSee('<meta property="og:image:type" content="image/png">', false);
        $response->assertSee('<meta property="og:image:width" content="1200">', false);
        $response->assertSee('<meta property="og:image:height" content="630">', false);
        $response->assertSee('<meta name="twitter:card" content="summary_large_image">', false);
    }

    /**
     * Rendering costs a headless Chrome, so the route only draws what this site
     * signed. Outside local development an unsigned request gets nothing.
     */
    public function test_the_image_route_turns_away_an_unsigned_request(): void
    {
        $this->get(route('og-image.file', ['title' => 'Free render, please']))
            ->assertForbidden();
    }

    /** The card itself: what the page it was drawn for gets to say on it. */
    public function test_the_card_prints_the_page_it_was_drawn_for(): void
    {
        $card = View::make('og-image::template', [
            'title' => 'Posts',
            'description' => 'Posts by Mark van Eijk on Laravel, PHP, React, Inertia and Tailwind CSS.',
            'path' => 'posts',
            'date' => '',
        ])->render();

        $this->assertStringContainsString('~ $ cat posts', $card);
        $this->assertStringContainsString('Posts<span class="cursor">_</span>', $card);
        $this->assertStringContainsString('Posts by Mark van Eijk on Laravel', $card);
        $this->assertStringContainsString('data:image/png;base64,', $card, 'The avatar is inlined; a URL Chrome cannot fetch fails the screenshot.');
    }

    /** The home page runs `whoami`, and a card without parameters still renders. */
    public function test_the_card_falls_back_to_the_site_itself(): void
    {
        $card = View::make('og-image::template', [])->render();

        $this->assertStringContainsString('~ $ whoami', $card);
        $this->assertStringContainsString('Mark van Eijk<span class="cursor">_</span>', $card);
    }

    /** The parameters the page asked the card to be drawn with. */
    private function cardParameters(string $html): array
    {
        parse_str((string) parse_url($this->cardUrl($html), PHP_URL_QUERY), $parameters);

        return $parameters;
    }

    private function cardUrl(string $html): string
    {
        preg_match('~<meta property="og:image" content="([^"]+)"~', $html, $matches);

        $this->assertNotEmpty($matches[1] ?? '', 'The page should carry an og:image.');
        $this->assertStringContainsString(route('og-image.file'), $matches[1], 'The og:image should be a card drawn for this page.');

        return html_entity_decode($matches[1], ENT_QUOTES);
    }
}
