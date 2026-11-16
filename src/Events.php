<?php

declare(strict_types=1);

namespace ItTour;

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

    /** City row of an event row, for links. */
    public static function city(array $event): array
    {
        return ['domain' => $event['city_domain'], 'slug' => $event['city_slug'], 'name' => $event['city_name']];
    }
}
