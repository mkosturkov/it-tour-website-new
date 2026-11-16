<?php

declare(strict_types=1);

namespace ItTour\Pages;

use ItTour\Cities;
use ItTour\Clock;
use ItTour\Events;
use ItTour\Page;
use ItTour\Request;
use ItTour\Schema;

/** it-tour.bg/archive: every edition, grouped by year, filtered by city and year. */
final class Archive
{
    public static function show(Request $req): Page
    {
        $cities = array_values(array_filter(Cities::all(), fn ($c) => $c['latest_name'] !== null));
        $city = in_array($req->q('city'), array_column($cities, 'slug'), true) ? $req->q('city') : null;
        $years = Events::allYears();
        $year = in_array((int) $req->q('year'), $years, true) ? (int) $req->q('year') : null;

        $all = Events::archive();
        $shown = Events::archive($city, $year);
        $byYear = [];
        foreach ($shown as $ev) {
            $byYear[$ev['year']][] = $ev;
        }

        // How far the current season is: "2 от 5 издания минаха".
        $now = Clock::now();
        $finished = [];
        foreach ($all as $ev) {
            $finished[$ev['year']] = ($finished[$ev['year']] ?? 0) + (Events::state($ev, $now) === 'finished' ? 1 : 0);
        }
        $perYear = array_count_values(array_column($all, 'year'));

        return new Page('pages/archive', [
            'title' => 'Архив',
            'description' => sprintf('Всички %d издания на IT Tour от %d насам: програми, лектори и видеа.', count($all), min($years)),
            'nav' => 'archive',
            'canonical' => main_url('/archive'),
            'jsonld' => [Schema::breadcrumbs([['IT Tour', main_url('/')], ['Архив', main_url('/archive')]])],
            'cities' => $cities,
            'years' => $years,
            'city' => $city,
            'year' => $year,
            'byYear' => $byYear,
            'total' => count($all),
            'cityCount' => count($cities),
            'firstYear' => min($years),
            'perYear' => $perYear,
            'finished' => $finished,
        ]);
    }
}
