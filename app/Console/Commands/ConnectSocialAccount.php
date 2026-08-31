<?php

namespace App\Console\Commands;

use App\Domain\Socials\Connections;
use App\Domain\Socials\ConnectsThroughOAuth;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Throwable;

/**
 * The whole "connect my account" flow, minus the one step a browser has to do.
 * Approving the app happens on the provider's own site; the code it hands back
 * is pasted in here, and the exchange for tokens runs on this machine.
 */
class ConnectSocialAccount extends Command
{
    protected $signature = 'socials:connect
                            {service? : spotify or strava}
                            {--code= : Skip the prompt with a code you already have}';

    protected $description = 'Connect a /now widget account over OAuth';

    public function handle(): int
    {
        $service = strtolower(trim(
            $this->argument('service') ?: $this->choice('Which account?', Connections::names())
        ));

        try {
            $connection = Connections::make($service);
        } catch (InvalidArgumentException $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        if (! $connection->isConfigured()) {
            $prefix = strtoupper($service);

            $this->components->error("{$prefix}_CLIENT_ID and {$prefix}_CLIENT_SECRET are missing from .env.");

            return self::FAILURE;
        }

        if ($connection->isConnected() && ! $this->confirm(
            ucfirst($service).' is already connected. Replace the stored tokens?', true
        )) {
            return self::SUCCESS;
        }

        $code = $this->option('code') ?: $this->askForCode($connection, $service);

        if (! $code) {
            return self::FAILURE;
        }

        try {
            $connection->connect($code);
        } catch (Throwable $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->components->info(ucfirst($service).' connected.');

        if ($summary = $connection->summarize()) {
            $this->components->twoColumnDetail('Reading', $summary);
        } else {
            $this->components->warn(
                'Tokens stored, but '.ucfirst($service).' did not answer a read. Check the scopes it granted.'
            );
        }

        return self::SUCCESS;
    }

    /**
     * The provider redirects a browser, not a terminal, so the round trip ends
     * with a copy-paste: the callback page prints the code, and the address bar
     * holds it either way.
     */
    private function askForCode(ConnectsThroughOAuth $connection, string $service): ?string
    {
        $state = Str::random(40);

        $this->components->twoColumnDetail('Redirect URI', $connection->redirectUri());
        $this->newLine();
        $this->line('Open this while signed in as yourself:');
        $this->newLine();
        $this->line('<options=bold>'.$connection->authorizeUrl($state).'</>');
        $this->newLine();

        $answer = trim((string) $this->ask('Paste the code you land on (or the whole URL)'));

        if ($answer === '') {
            $this->components->error('Nothing pasted.');

            return null;
        }

        if (! Str::startsWith($answer, ['http://', 'https://'])) {
            return $answer;
        }

        parse_str((string) parse_url($answer, PHP_URL_QUERY), $query);

        if (isset($query['error'])) {
            $this->components->error(ucfirst($service).' declined the request: '.$query['error']);

            return null;
        }

        // Whoever started the flow is whoever finishes it; a URL carrying
        // someone else's state was not the one this command handed out.
        if (($query['state'] ?? $state) !== $state) {
            $this->components->error('That URL came back with a state this command never sent. Start again.');

            return null;
        }

        if (empty($query['code'])) {
            $this->components->error('No code in that URL.');

            return null;
        }

        return (string) $query['code'];
    }
}
