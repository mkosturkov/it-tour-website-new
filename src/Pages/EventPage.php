<?php

declare(strict_types=1);

namespace ItTour\Pages;

use ItTour\Clock;
use ItTour\Events;
use ItTour\Media;
use ItTour\Page;
use ItTour\Programme;
use ItTour\Site;
use ItTour\Sponsors;

/**
 * An edition on its city's domain: "/" shows the latest published edition,
 * "/archive/<year>" any edition. It is the same page before, during and after
 * the event; the parts shown follow the event's state.
 */
final class EventPage
{
    public static function show(Site $site, ?int $year = null): ?Page
    {
        $event = Events::forCity((int) $site->city['id'], $year);
        if (!$event) {
            return null;
        }
        $now = Clock::now();
        $state = Events::state($event, $now);
        $slots = $event['schedule_published'] ? Programme::slots((int) $event['id']) : [];
        $talks = array_filter(array_column($slots, 'talk'));

        $season = Events::ofYear((int) $event['year']);
        $stop = array_search($event['id'], array_column($season, 'id'), true);

        $latest = Events::forCity((int) $site->city['id']);
        $isLatest = $latest && $latest['id'] === $event['id'];
        $first = $slots ? $slots[0]['starts_at'] : $event['starts_at'];

        return new Page('pages/event', [
            'title' => $event['name'],
            'description' => sprintf(
                '%s: безплатна IT конференция, %s, %s.',
                $event['name'],
                date_bg($event['date'], 'long'),
                $event['venue_name'] ?: $event['city_name'],
            ),
            // The latest edition lives at "/" and also answers at /archive/<year>.
            'canonical' => $site->url($isLatest ? '/' : '/archive/' . $event['year']),
            'colour' => $event['colour'],
            'event' => $event,
            'state' => $state,
            'now' => $now,
            'logo' => Media::find($event['logo_media_id']),
            'recordings' => json_decode($event['recordings'] ?? '[]', true) ?: [],
            'wordmark' => Events::wordmark($event),
            'season' => $season,
            'stop' => $stop === false ? null : $stop + 1,
            'slots' => $slots,
            'speakers' => Programme::speakers($slots),
            'onStage' => $state === 'live' ? Programme::onStage($slots, $now->format('H:i')) : null,
            'talkCount' => count($talks),
            'videoCount' => count(array_filter(array_column($talks, 'video_url'))),
            'sponsors' => Sponsors::forEvent((int) $event['id']),
            'years' => Events::years((int) $site->city['id']),
            'doorsOpen' => $first,
            'cfpOpen' => $event['cfs_deadline'] && $now->format('Y-m-d') <= $event['cfs_deadline'],
        ]);
    }
}
