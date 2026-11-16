<?php

declare(strict_types=1);

namespace ItTour\Pages;

use ItTour\Events;
use ItTour\Page;

/** Temporary main-site home page until the real one is built (step 7). */
final class Placeholder
{
    public static function main(): Page
    {
        return new Page('pages/placeholder', [
            'title' => 'IT Tour',
            'heading' => 'IT Tour',
            'season' => Events::season(),
        ]);
    }

}
