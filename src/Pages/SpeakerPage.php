<?php

declare(strict_types=1);

namespace ItTour\Pages;

use ItTour\Events;
use ItTour\Page;
use ItTour\Schema;
use ItTour\Site;
use ItTour\Speakers;
use ItTour\Talks;

/**
 * /speakers/<id>: a speaker's profile and all their talks. It answers on every
 * domain (old addresses such as varnaconf.com/speakers/127 keep working); the
 * canonical address is on it-tour.bg.
 */
final class SpeakerPage
{
    public static function show(Site $site, int $id): ?Page
    {
        $speaker = Speakers::find($id);
        if (!$speaker) {
            return null;
        }
        $talks = Talks::forSpeaker($id);
        $upcoming = array_values(array_filter($talks, fn ($t) => Events::state($t['event']) === 'upcoming'));
        $past = array_values(array_filter($talks, fn ($t) => Events::state($t['event']) !== 'upcoming'));
        return new Page('pages/speaker', [
            'title' => $speaker['name'],
            'description' => excerpt($speaker['bio'] ?: $speaker['name'] . ', лектор на IT Tour.', 200),
            'nav' => 'speakers',
            'canonical' => main_url('/speakers/' . $id),
            'ogType' => 'profile',
            'image' => $speaker['photo'],
            'imageAlt' => $speaker['name'],
            'jsonld' => [
                Schema::profile($speaker, main_url('/speakers/' . $id)),
                Schema::breadcrumbs([['IT Tour', main_url('/')], ['Лектори', main_url('/speakers')], [$speaker['name'], main_url('/speakers/' . $id)]]),
            ],
            'speaker' => $speaker,
            'upcoming' => $upcoming,
            'past' => $past,
            'talkCount' => count($talks),
        ]);
    }
}
