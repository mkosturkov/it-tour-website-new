<?php

declare(strict_types=1);

namespace ItTour\Pages;

use ItTour\Db;
use ItTour\Events;
use ItTour\Page;
use ItTour\Site;

/** Temporary home pages until the real ones are built (steps 3 and 7). */
final class Placeholder
{
    public static function main(): Page
    {
        return new Page('pages/placeholder', [
            'title' => 'IT Tour',
            'heading' => 'IT Tour',
            'season' => Events::season(),
        ]);
    }

    public static function city(Site $site): Page
    {
        $event = Db::one('SELECT * FROM events WHERE city_id = ? AND published = 1 ORDER BY year DESC LIMIT 1', [$site->city['id']]);
        return new Page('pages/placeholder', [
            'title' => $event['name'] ?? $site->city['name'],
            'heading' => $event['name'] ?? $site->city['name'],
            'season' => Events::season(),
        ]);
    }
}
