<?php

declare(strict_types=1);

namespace ItTour\Pages;

use ItTour\Cities;
use ItTour\Events;
use ItTour\Page;
use ItTour\Pager;
use ItTour\Request;
use ItTour\Speakers;

/**
 * it-tour.bg/speakers: everyone who has spoken (or will speak) at a published
 * edition, alphabetical, searchable by name and filtered by city and year,
 * in numbered pages.
 * With "X-Partial: results" only the results block is returned.
 */
final class SpeakersDirectory
{
    public static function show(Request $req): ?Page
    {
        $page = Pager::number($req);
        if ($page === null) {
            return null;
        }
        $cities = array_values(array_filter(Cities::all(), fn ($c) => $c['latest_name'] !== null));
        $years = Events::allYears();
        $f = [
            'q' => mb_substr($req->q('q'), 0, 100),
            'city' => in_array($req->q('city'), array_column($cities, 'slug'), true) ? $req->q('city') : null,
            'year' => in_array((int) $req->q('year'), $years, true) ? (int) $req->q('year') : null,
        ];
        [$total, $speakers] = Speakers::directory($f, ($page - 1) * Pager::PER_PAGE, Pager::PER_PAGE);
        $pager = new Pager($total, $page);
        if (!$pager->exists()) {
            return null;
        }
        $all = array_filter($f) === [] ? $total : Speakers::directory([], 0, 0)[0];

        return new Page('pages/speakers', [
            'title' => $page > 1 ? "Лектори, страница $page" : 'Лектори',
            'description' => sprintf('%d души са говорили на IT Tour конференциите. Всеки има профил с всичките си лекции.', $all),
            'nav' => 'speakers',
            'canonical' => list_url('/speakers', $f + ['page' => $page]),
            'noindex' => array_filter($f) !== [],
            'filters' => $f,
            'cities' => $cities,
            'years' => $years,
            'all' => $all,
            'speakers' => $speakers,
            'pager' => $pager,
        ], partial: 'partials/speaker-results');
    }
}
