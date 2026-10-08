<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** `whoami` on the home page answers Galileo; the share card is left as it was. */
    public function test_whoami_on_the_home_page_prints_galileo()
    {
        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder(['$ whoami', '<h1', '>Galileo<', '</h1>'], false);
    }
}
