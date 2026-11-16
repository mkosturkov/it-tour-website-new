<?php

declare(strict_types=1);

namespace ItTour;

use DateTimeImmutable;
use DateTimeZone;

/**
 * The current time in Bulgaria, which tests can fake: config 'now', the
 * IT_TOUR_NOW environment variable, or (only when IT_TOUR_TESTING=1) an
 * X-Test-Now request header, so one test server can show any moment.
 */
final class Clock
{
    private static ?string $fake = null;

    public static function fake(?string $when): void
    {
        self::$fake = $when;
    }

    public static function zone(): DateTimeZone
    {
        return new DateTimeZone('Europe/Sofia');
    }

    public static function now(): DateTimeImmutable
    {
        $fake = self::$fake ?: getenv('IT_TOUR_NOW') ?: Config::get('now');
        return new DateTimeImmutable($fake ?: 'now', self::zone());
    }
}
