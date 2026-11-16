<?php

declare(strict_types=1);

namespace ItTour\Pages;

use ItTour\Cities;
use ItTour\Events;
use ItTour\Page;
use ItTour\Pager;
use ItTour\Request;
use ItTour\Talks;

/**
 * it-tour.bg/talks: every public talk, searchable and filtered by city, year
 * and "only with a recording", in numbered pages. With "X-Partial: results"
 * only the results block is returned (instant search in site.js).
 */
final class TalksLibrary
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
            'video' => $req->q('video') === '1' ? 1 : null,
        ];
        [$total, $talks] = Talks::library($f, ($page - 1) * Pager::PER_PAGE, Pager::PER_PAGE);
        $pager = new Pager($total, $page);
        if (!$pager->exists()) {
            return null;
        }
        $filtered = array_filter($f) !== [];
        $counts = Talks::counts();

        return new Page('pages/talks', [
            'title' => $page > 1 ? "Лекции, страница $page" : 'Лекции',
            'description' => sprintf('%d лекции от IT Tour конференциите от %d насам, %d със запис. Търси по заглавие или лектор.', $counts['talks'], min($years), $counts['videos']),
            'nav' => 'talks',
            'canonical' => list_url('/talks', $f + ['page' => $page]),
            // Search and filter results are near-copies of the full list: crawl them, don't index them.
            'noindex' => $filtered,
            'filters' => $f,
            'cities' => $cities,
            'years' => $years,
            'counts' => $counts,
            'talks' => $talks,
            'pager' => $pager,
        ], partial: 'partials/talk-results');
    }
}
