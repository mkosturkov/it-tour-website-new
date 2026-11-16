<?php

declare(strict_types=1);

namespace ItTour;

/**
 * The homepage map of Bulgaria (from design F): an outline in a 1000 × 680 box
 * and a straight-line projection of latitude and longitude onto it, which is
 * close enough at this size.
 */
final class Map
{
    public const WIDTH = 1000;
    public const HEIGHT = 680;

    /** Simplified outline of Bulgaria, in the same box as point(). */
    public const OUTLINE = 'M72 39 L120 113 L210 125 L315 144 L420 146 L495 156 L565 107 L645 72 L720 55 L780 80 L870 134 L957 136 L960 185 L885 206 L862 247 L855 308 L833 349 L795 397 L825 411 L862 452 L870 493 L810 503 L750 483 L660 503 L623 545 L585 627 L510 633 L450 647 L345 586 L270 606 L210 621 L112 627 L112 575 L83 493 L30 432 L45 390 L38 319 L83 247 L112 185 L45 123 Z';

    /** [x, y] in the map box for a latitude and longitude. */
    public static function point(float $lat, float $lng): array
    {
        return [(int) round(150 * $lng - 3329.5), (int) round(9058.6 - 204 * $lat)];
    }
}
