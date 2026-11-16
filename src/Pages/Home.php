<?php

declare(strict_types=1);

namespace ItTour\Pages;

use ItTour\Clock;
use ItTour\Events;
use ItTour\Map;
use ItTour\Page;
use ItTour\Season;
use ItTour\Settings;
use ItTour\Speakers;
use ItTour\Sponsors;
use ItTour\Talks;

/**
 * it-tour.bg: what IT Tour is, this season's stops (map and list), recent
 * videos, the season's sponsors and the newsletter. News and "get involved"
 * come in the next version (decisions.md, "Version 1").
 */
final class Home
{
    public static function show(): Page
    {
        $now = Clock::now();
        $season = Season::current($now);
        $finished = count(array_filter(Events::archive(), fn ($ev) => Events::state($ev, $now) === 'finished'));
        $counts = Talks::counts();
        $years = Events::allYears();

        // Map: a dot per stop, joined in date order; the line is solid up to the next stop.
        $points = [];
        foreach ($season['events'] as $ev) {
            if ($ev['city_lat'] !== null) {
                [$x, $y] = Map::point((float) $ev['city_lat'], (float) $ev['city_lng']);
                $points[] = ['x' => $x, 'y' => $y, 'event' => $ev];
            }
        }

        return new Page('pages/home', [
            'title' => 'IT Tour: безплатни IT конференции из България',
            'description' => Settings::get('tagline'),
            'tagline' => Settings::get('tagline'),
            'nav' => null,
            'canonical' => main_url('/'),
            'season' => $season,
            'points' => $points,
            'facts' => [
                'editions' => $finished,
                'talks' => (int) $counts['talks'],
                'speakers' => Speakers::directory([], 0, 0)[0],
                'since' => $years ? min($years) : (int) $now->format('Y'),
            ],
            'videos' => Settings::on('home_videos') ? Talks::recentVideos(4) : [],
            'sponsors' => Sponsors::forSeason($season['year']),
            'newsletter' => Settings::get('newsletter_url'),
        ]);
    }
}
