<?php

namespace App\Console\Commands;

use App\Domain\Socials\Connections;
use Illuminate\Console\Command;
use InvalidArgumentException;

class DisconnectSocialAccount extends Command
{
    protected $signature = 'socials:disconnect {service? : spotify or strava}';

    protected $description = 'Throw away the stored tokens for a /now widget account';

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

        if (! $connection->isConnected()) {
            $this->components->info(ucfirst($service).' is not connected.');

            return self::SUCCESS;
        }

        if (! $this->confirm('Forget the '.ucfirst($service).' tokens?', true)) {
            return self::SUCCESS;
        }

        $connection->disconnect();

        $this->components->info(ucfirst($service).' disconnected — its widget hides itself until you connect again.');

        return self::SUCCESS;
    }
}
