<?php

declare(strict_types=1);

namespace ItTour;

/** Queries about cities. */
final class Cities
{
    private static ?array $all = null;

    /**
     * All cities in season order, each with the colour of its latest published
     * edition and its brand name from that edition ("VarnaConf 2026" → "VarnaConf").
     */
    public static function all(): array
    {
        if (self::$all === null) {
            self::$all = Db::all(
                'SELECT c.*,
                    (SELECT e.colour FROM events e WHERE e.city_id = c.id AND e.published = 1 ORDER BY e.year DESC LIMIT 1) AS colour,
                    (SELECT e.name FROM events e WHERE e.city_id = c.id AND e.published = 1 ORDER BY e.year DESC LIMIT 1) AS latest_name
                 FROM cities c ORDER BY c.sort_order'
            );
            foreach (self::$all as &$c) {
                $c['brand'] = $c['latest_name'] ? trim(preg_replace('/\s*\d{4}$/', '', $c['latest_name'])) : $c['name'];
            }
            unset($c);
        }
        return self::$all;
    }

    public static function active(): array
    {
        return array_values(array_filter(self::all(), fn ($c) => $c['active']));
    }

    /** Forgets cached rows, for tests that swap the database. */
    public static function reset(): void
    {
        self::$all = null;
    }
}
