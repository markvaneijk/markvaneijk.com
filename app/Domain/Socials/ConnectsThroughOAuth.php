<?php

namespace App\Domain\Socials;

/**
 * A /now widget whose credentials come from an OAuth redirect. Approving the
 * app still happens in a browser — no way around that — but every step that
 * touches a token runs from `php artisan socials:*`, never from a web request,
 * so the site carries no "connect this account" flow of its own.
 */
interface ConnectsThroughOAuth
{
    /** True once the client id and secret are in the environment. */
    public function isConfigured(): bool;

    /** Where the provider sends the browser back to; must match its dashboard. */
    public function redirectUri(): string;

    /** Where to send the browser. $state comes back on the redirect. */
    public function authorizeUrl(string $state): string;

    /**
     * Trade the code from the redirect for tokens and store them.
     *
     * @throws \RuntimeException when the provider refuses the code.
     */
    public function connect(string $code): void;

    /** True when a token is stored — not that the provider still honours it. */
    public function isConnected(): bool;

    /** Drop the stored tokens and everything cached behind them. */
    public function disconnect(): void;

    /** One line proving the stored token still reads, or null when it does not. */
    public function summarize(): ?string;
}
