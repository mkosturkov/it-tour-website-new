<?php

declare(strict_types=1);

namespace ItTour;

/** Numbered pages for a long list: which rows to show and which page links to print. */
final class Pager
{
    public const PER_PAGE = 24;

    public readonly int $pages;

    public function __construct(
        public readonly int $total,
        public readonly int $page,
        public readonly int $perPage = self::PER_PAGE,
    ) {
        $this->pages = max(1, (int) ceil($total / $perPage));
    }

    /**
     * The page number from ?page=: 1 when missing, null when it isn't a
     * positive whole number (the handler answers 404).
     */
    public static function number(Request $req): ?int
    {
        $p = $req->q('page');
        if ($p === '') {
            return 1;
        }
        return ctype_digit($p) && (int) $p >= 1 && strlen($p) < 7 ? (int) $p : null;
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }

    /** False for a page past the end (404); page 1 of an empty list exists. */
    public function exists(): bool
    {
        return $this->page <= $this->pages;
    }

    /** Page numbers to link, null for a gap: [1, null, 4, 5, 6, null, 20]. */
    public function links(): array
    {
        $show = array_unique([1, $this->page - 1, $this->page, $this->page + 1, $this->pages]);
        $show = array_filter($show, fn ($n) => $n >= 1 && $n <= $this->pages);
        sort($show);
        $out = [];
        $prev = 0;
        foreach ($show as $n) {
            if ($n - $prev === 2) {
                $out[] = $n - 1;   // a gap of one page: show the page instead of "…"
            } elseif ($n - $prev > 2) {
                $out[] = null;
            }
            $out[] = $n;
            $prev = $n;
        }
        return $out;
    }
}
