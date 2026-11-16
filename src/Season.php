<?php

declare(strict_types=1);

namespace ItTour;

use DateTimeImmutable;

/**
 * This season (the latest year with published events) as a whole: each stop's
 * state, the next stop and the season's state (decisions.md, "Season state is
 * automatic"):
 *  - 'live'      an event is on now (between its start and end time)
 *  - 'upcoming'  some stop hasn't happened yet
 *  - 'off'       every stop is over, until next year's first event is published
 */
final class Season
{
    public static function current(?DateTimeImmutable $now = null): array
    {
        $now ??= Clock::now();
        $events = Events::season();
        $next = null;
        foreach ($events as $i => &$ev) {
            $ev['state'] = Events::state($ev, $now);
            $ev['logo'] = Media::find($ev['logo_media_id']);
            if ($next === null && $ev['state'] !== 'finished') {
                $next = $i;
            }
        }
        unset($ev);
        return [
            'year' => $events ? (int) $events[0]['year'] : (int) $now->format('Y'),
            'events' => $events,
            'next' => $next === null ? null : $events[$next],
            'nextIndex' => $next,
            'state' => $next === null ? 'off' : ($events[$next]['state'] === 'live' ? 'live' : 'upcoming'),
        ];
    }

    /**
     * The small season pop-up on main-site pages: the next stop, or the event
     * that is live now with what's on stage. Null off-season.
     */
    public static function popup(?DateTimeImmutable $now = null): ?array
    {
        $now ??= Clock::now();
        $season = self::current($now);
        $ev = $season['next'];
        if (!$ev) {
            return null;
        }
        $live = $season['state'] === 'live';
        $slots = $ev['schedule_published'] ? Programme::slots((int) $ev['id']) : [];
        $days = (int) $now->setTime(0, 0)->diff(new DateTimeImmutable($ev['date'], Clock::zone()))->format('%r%a');
        return [
            'event' => $ev,
            'live' => $live,
            'days' => $days,
            'hasSchedule' => $slots !== [],
            'onStage' => $live ? Programme::onStage($slots, $now->format('H:i')) : null,
        ];
    }
}
