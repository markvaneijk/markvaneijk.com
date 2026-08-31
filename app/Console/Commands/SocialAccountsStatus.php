<?php

namespace App\Console\Commands;

use App\Domain\Socials\Connections;
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
                match (true) {
                    ! $connection->isConnected() => 'run socials:connect '.$service,
                    default => $connection->summarize() ?? 'no answer — reconnect',
                },
            ];
        }

        $this->table(['Account', 'Credentials', 'Tokens', 'Reading'], $rows);

        return self::SUCCESS;
    }
}
