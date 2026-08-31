<?php

namespace App\Domain\Socials;

use App\Domain\Socials\Clients\GitHub;
use App\Domain\Socials\Clients\Spotify;
use App\Domain\Socials\Clients\Strava;
use App\Domain\Socials\Clients\X;
use InvalidArgumentException;

/**
 * The accounts the `socials:*` commands know how to connect. Last.fm is missing
 * on purpose: it reads with a plain API key from the environment, so there is
 * nothing to authorize. GitHub takes either — connecting it beats a personal
 * access token, which never expires and cannot be scoped as tightly.
 */
class Connections
{
    private const SERVICES = [
        'github' => GitHub::class,
        'spotify' => Spotify::class,
        'strava' => Strava::class,
        'x' => X::class,
    ];

    /** @return array<int, string> */
    public static function names(): array
    {
        return array_keys(self::SERVICES);
    }

    public static function make(string $service): ConnectsThroughOAuth
    {
        $client = self::SERVICES[strtolower(trim($service))] ?? throw new InvalidArgumentException(
            "Unknown account [{$service}]. Pick one of: ".implode(', ', self::names()).'.'
        );

        return new $client(Store::make());
    }
}
