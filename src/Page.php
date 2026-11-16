<?php

declare(strict_types=1);

namespace ItTour;

/**
 * What a page handler returns: a template and its plain data. Rendering it is
 * a separate, pure step, so a page cache can wrap it later.
 */
final class Page
{
    public function __construct(
        public readonly string $template,
        public readonly array $data = [],
        public readonly int $status = 200,
        /** Template rendered alone for X-Partial: results, if the page has one. */
        public readonly ?string $partial = null,
    ) {
    }
}
