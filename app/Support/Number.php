<?php

namespace App\Support;

/**
 * Numbers written the way the language reading them writes them: 1,234 and
 * 15.3 in English, 1.234 and 15,3 in Dutch.
 *
 * PHP's number_format() takes both separators, so the language file is all
 * this needs — no ext-intl, which a machine serving the site may well not
 * have and which the /now widgets would fatal on the moment it was missing.
 */
class Number
{
    public static function format(int|float $value, int $decimals = 0): string
    {
        return number_format(
            $value,
            $decimals,
            __('site.numbers.decimal'),
            __('site.numbers.thousands'),
        );
    }
}
