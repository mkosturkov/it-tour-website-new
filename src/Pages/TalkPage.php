<?php

declare(strict_types=1);

namespace ItTour\Pages;

use ItTour\Events;
use ItTour\Page;
use ItTour\Request;
use ItTour\Response;
use ItTour\Talks;

/** it-tour.bg/talks/<id>-<slug>: one talk. The number finds it; a wrong slug redirects. */
final class TalkPage
{
    public static function show(Request $req, int $id, string $slug): Page|Response|null
    {
        $talk = Talks::find($id);
        if (!$talk) {
            return null;
        }
        if ($slug !== $talk['slug']) {
            return Response::redirect(talk_url($talk));
        }
        $ev = $talk['event'];
        $names = implode(', ', array_column($talk['speakers'], 'name'));
        return new Page('pages/talk', [
            'title' => $talk['title'],
            'description' => excerpt($talk['description'] ?: sprintf('%s%s на %s.', $talk['title'], $names ? ", $names," : '', $ev['name']), 200),
            'nav' => 'talks',
            'canonical' => talk_url($talk),
            'talk' => $talk,
            'event' => $ev,
            'eventUrl' => city_url(['domain' => $ev['city_domain']], '/archive/' . $ev['year']),
            'others' => Talks::sameEvent((int) $ev['id'], $id),
            'upcoming' => Events::state($ev) === 'upcoming',
        ]);
    }
}
