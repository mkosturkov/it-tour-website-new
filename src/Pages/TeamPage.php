<?php

declare(strict_types=1);

namespace ItTour\Pages;

use ItTour\Db;
use ItTour\Media;
use ItTour\Page;
use ItTour\Schema;

/**
 * it-tour.bg/team: everyone on the team in one flat list, in the editors'
 * order (decisions.md: layout B, no grouping by city, no team elsewhere).
 */
final class TeamPage
{
    public static function show(): Page
    {
        $people = array_map(
            fn ($r) => Media::pluck($r, 'photo'),
            Db::all(
                'SELECT tm.*, ' . Media::JOIN_COLUMNS . '
                 FROM team_members tm LEFT JOIN media m ON m.id = tm.photo_media_id
                 WHERE tm.published = 1 ORDER BY tm.sort_order, tm.name'
            ),
        );
        return new Page('pages/team', [
            'title' => 'Екип',
            'description' => 'Хората зад IT Tour: доброволци, които организират конференциите от 2012 г. насам.',
            'nav' => 'team',
            'canonical' => main_url('/team'),
            'jsonld' => [Schema::breadcrumbs([['IT Tour', main_url('/')], ['Екип', main_url('/team')]])],
            'people' => $people,
        ]);
    }
}
