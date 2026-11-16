<?php

declare(strict_types=1);

namespace ItTour;

use DateTimeImmutable;

/** Queries about events (editions). Only published events. */
final class Events
{
    private const SELECT = 'SELECT e.*, c.slug AS city_slug, c.name AS city_name, c.domain AS city_domain
                            FROM events e JOIN cities c ON c.id = e.city_id';

    /** This season: the published events of the latest year that has any, in date order. */
    public static function season(): array
    {
        return Db::all(self::SELECT . ' WHERE e.published = 1
            AND e.year = (SELECT MAX(year) FROM events WHERE published = 1)
            ORDER BY e.date');
    }

    /** All published events of a year, in date order. */
    public static function ofYear(int $year): array
    {
        return Db::all(self::SELECT . ' WHERE e.published = 1 AND e.year = ? ORDER BY e.date', [$year]);
    }

    /** A city's published edition of a year, or its latest one when $year is null. */
    public static function forCity(int $cityId, ?int $year = null): ?array
    {
        return $year === null
            ? Db::one(self::SELECT . ' WHERE e.published = 1 AND e.city_id = ? ORDER BY e.year DESC LIMIT 1', [$cityId])
            : Db::one(self::SELECT . ' WHERE e.published = 1 AND e.city_id = ? AND e.year = ?', [$cityId, $year]);
    }

    /** Years of a city's published editions, newest first. */
    public static function years(int $cityId): array
    {
        return array_map('intval', array_column(
            Db::all('SELECT year FROM events WHERE published = 1 AND city_id = ? ORDER BY year DESC', [$cityId]),
            'year',
        ));
    }

    /** City row of an event row, for links. */
    public static function city(array $event): array
    {
        return ['domain' => $event['city_domain'], 'slug' => $event['city_slug'], 'name' => $event['city_name']];
    }

    public static function startsAt(array $event): DateTimeImmutable
    {
        return new DateTimeImmutable($event['date'] . ' ' . $event['starts_at'], Clock::zone());
    }

    public static function endsAt(array $event): DateTimeImmutable
    {
        return new DateTimeImmutable($event['date'] . ' ' . $event['ends_at'], Clock::zone());
    }

    /**
     * 'upcoming' before the start, 'live' between start and end time (Bulgarian
     * time; the after party doesn't count), 'finished' afterwards.
     */
    public static function state(array $event, ?DateTimeImmutable $now = null): string
    {
        $now ??= Clock::now();
        return match (true) {
            $now < self::startsAt($event) => 'upcoming',
            $now < self::endsAt($event) => 'live',
            default => 'finished',
        };
    }

    /** "VarnaConf 2026" → ["varna", "conf 2026"], for the two-line wordmark. */
    public static function wordmark(array $event): array
    {
        if (preg_match('/^(.*?)(conf)\s*(\d{4})?$/i', $event['name'], $m)) {
            return [mb_strtolower($m[1]), trim(mb_strtolower($m[2]) . ' ' . ($m[3] ?? ''))];
        }
        return [mb_strtolower($event['name']), ''];
    }
}
