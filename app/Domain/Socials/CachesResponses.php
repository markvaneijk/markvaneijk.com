<?php

namespace App\Domain\Socials;

/**
 * The /now widgets render server-side, so every API call sits in front of a
 * page load. Cache what came back — and, for a shorter while, the fact that
 * nothing came back, so a dead API costs one slow request instead of all of them.
 */
trait CachesResponses
{
    protected function remember(string $key, int $seconds, callable $fetch, int $failureSeconds = 900): mixed
    {
        $store = Store::make();
        $cached = $store->get($key);

        // `false` is the marker for a fetch that failed; no client returns it.
        if ($cached !== null) {
            return $cached === false ? null : $cached;
        }

        try {
            $value = $fetch();
        } catch (\Throwable) {
            $value = null;
        }

        $store->put($key, $value ?? false, $value === null ? $failureSeconds : $seconds);

        return $value;
    }
}
