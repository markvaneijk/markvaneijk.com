<?php

namespace App\Console\Commands;

use App\Domain\Socials\Connections;
use App\Domain\Socials\Store;
use Illuminate\Console\Command;

/**
 * What the browser round trip used to prove by rendering /now: whether the
 * credentials are there, whether a token is stored, and whether that token
 * still reads anything.
 */
class SocialAccountsStatus extends Command
{
    protected $signature = 'socials:status';

    protected $description = 'Show which /now widget accounts are connected';

    public function handle(): int
    {
        $rows = [];

        foreach (Connections::names() as $service) {
            $connection = Connections::make($service);

            $rows[] = [
                $service,
                $connection->isConfigured() ? 'in .env' : 'missing',
                $connection->isConnected() ? 'stored' : '—',
                // Whether it reads comes first: GitHub also takes a token
                // straight from the environment, and a working widget should
                // not be told to go and connect itself.
                match (true) {
                    ($summary = $connection->summarize()) !== null => $summary,
                    ! $connection->isConnected() => 'run socials:connect '.$service,
                    default => 'no answer — reconnect',
                },
            ];
        }

        $this->table(['Account', 'Credentials', 'Tokens', 'Reading'], $rows);

        if (Store::sharesDefaultStore()) {
            $this->warn(
                'Tokens are in the ['.Store::name().'] cache store, the one `cache:clear` empties — '
                .'a deploy will disconnect these accounts. Point SOCIALS_STORE at a store of its own.'
            );
        }

        return self::SUCCESS;
    }
}
