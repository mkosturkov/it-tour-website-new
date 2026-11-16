<?php

declare(strict_types=1);

namespace ItTour;

use DateTimeImmutable;
use DateTimeZone;

/** The current time in Bulgaria, which tests can fake (config 'now' or IT_TOUR_NOW). */
final class Clock
{
    public static function zone(): DateTimeZone
    {
        return new DateTimeZone('Europe/Sofia');
    }

    public static function now(): DateTimeImmutable
    {
        $fake = getenv('IT_TOUR_NOW') ?: Config::get('now');
        return new DateTimeImmutable($fake ?: 'now', self::zone());
    }
}
