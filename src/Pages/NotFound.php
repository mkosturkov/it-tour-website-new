<?php

declare(strict_types=1);

namespace ItTour\Pages;

use ItTour\Events;
use ItTour\Page;

/** The 404 page: a short message and links to this season, the archive, talks and speakers. */
final class NotFound
{
    public static function page(): Page
    {
        return new Page('pages/not-found', [
            'title' => 'Страницата не е намерена',
            'season' => Events::season(),
        ], 404);
    }
}
