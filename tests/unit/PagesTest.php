<?php
/**
 * Page tests in PHP for data the frozen test database doesn't have (hidden
 * after party, unpublished items). Each test works on a throwaway copy.
 */

use ItTour\App;
use ItTour\Cities;
use ItTour\Db;
use ItTour\Request;
use ItTour\Settings;

/** Runs $sql on a copy of the test database, then requests $uri on $host. */
function page_with(string $sql, string $host, string $uri, array $headers = []): ItTour\Response
{
    $copy = sys_get_temp_dir() . '/it-tour-page-test-' . getmypid() . '.sqlite';
    copy(APP_ROOT . '/data/test/site.sqlite', $copy);
    putenv("IT_TOUR_DB=$copy");
    Db::close();
    Cities::reset();
    Settings::reset();
    if ($sql !== '') {
        Db::pdo()->exec($sql);
    }
    try {
        return App::handle(Request::get($host, $uri, $headers));
    } finally {
        Db::close();
        putenv('IT_TOUR_DB');
        @unlink($copy);
    }
}

$main = ItTour\Config::get('main_host');
$varna = 'varnaconf.' . explode('.', ItTour\Config::get('main_host'), 2)[1];

if (!is_file(APP_ROOT . '/data/test/site.sqlite')) {
    return [];
}

return [
    'a hidden after party disappears completely' => function () use ($varna) {
        $shown = page_with('', $varna, '/archive/2024');
        ok(str_contains($shown->body, 'id="after-party"'), 'after party should show');
        $hidden = page_with("UPDATE events SET party_visible = 0 WHERE year = 2024 AND city_id = (SELECT id FROM cities WHERE slug = 'varna')", $varna, '/archive/2024');
        ok(!str_contains($hidden->body, 'id="after-party"'), 'section still there');
        ok(!str_contains($hidden->body, 'after party'), 'still mentioned');
    },
    'an unpublished programme is not shown' => function () use ($varna) {
        $res = page_with("UPDATE events SET schedule_published = 0 WHERE year = 2024", $varna, '/archive/2024');
        same(200, $res->status);
        ok(!str_contains($res->body, 'id="schedule"'), 'schedule shown');
        ok(!str_contains($res->body, 'id="speakers"'), 'speakers shown');
    },
    'an unpublished edition is a 404, and "/" falls back to the previous one' => function () use ($varna) {
        $sql = "UPDATE events SET published = 0 WHERE year = 2026";
        same(404, page_with($sql, $varna, '/archive/2026')->status);
        ok(str_contains(page_with($sql, $varna, '/')->body, 'VarnaConf <span>2025</span>'), '"/" should show 2025');
    },
    'an unpublished speaker is left out of the talk' => function () use ($varna) {
        $res = page_with('UPDATE speakers SET published = 0 WHERE id = 161', $varna, '/archive/2024');
        ok(!str_contains($res->body, 'Богомил Шопов'), 'unpublished speaker shown');
        ok(str_contains($res->body, 'Два проекта с отворен код'), 'the talk itself should stay');
    },
    'live block: watch here or on YouTube, external player link, now on stage' => function () use ($varna) {
        putenv('IT_TOUR_NOW=2026-08-22 11:20');
        try {
            $res = page_with("UPDATE events SET live_url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', stream_url = 'https://stream.example/live.m3u8' WHERE year = 2026", $varna, '/');
        } finally {
            putenv('IT_TOUR_NOW');
        }
        ok(str_contains($res->body, 'id="live"'), 'no live block');
        ok(str_contains($res->body, 'data-embed="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ"'), 'player address (no autoplay)');
        ok(str_contains($res->body, 'Гледай в YouTube'), 'no YouTube link');
        ok(str_contains($res->body, 'https://stream.example/live.m3u8'), 'no external player link');
        ok(str_contains($res->body, 'class="onstage"'), 'no now on stage');
    },
    'text from the database is escaped' => function () use ($varna) {
        $res = page_with("UPDATE talks SET title = '<script>alert(1)</script>' WHERE id = (SELECT talk_id FROM slots s JOIN events e ON e.id = s.event_id JOIN cities c ON c.id = e.city_id WHERE e.year = 2024 AND c.slug = 'varna' AND talk_id IS NOT NULL LIMIT 1)", $varna, '/archive/2024');
        ok(!str_contains($res->body, '<script>alert(1)'), 'not escaped');
        ok(str_contains($res->body, '&lt;script&gt;alert(1)'), 'escaped title missing');
    },
    'lists: instant search gets only the results block, and caches keep both' => function () use ($main) {
        $full = page_with('', $main, '/talks?q=nakov');
        $part = page_with('', $main, '/talks?q=nakov', ['X-Partial' => 'results']);
        same('X-Partial', $full->headers['Vary'] ?? null);
        same('X-Partial', $part->headers['Vary'] ?? null);
        ok(str_contains($full->body, '<html'), 'full page expected');
        ok(!str_contains($part->body, '<html') && str_contains($part->body, 'class="count-line"'), 'results block expected');
    },
    'lists: filtered results are "noindex, follow"; the plain list is indexed' => function () use ($main) {
        ItTour\Config::override(['production' => true]);
        try {
            foreach (['/talks', '/talks?page=2', '/speakers'] as $uri) {
                ok(!str_contains(page_with('', $main, $uri)->body, 'name="robots"'), "$uri should be indexed");
            }
            foreach (['/talks?q=go', '/talks?city=varna', '/talks?video=1', '/speakers?city=ruse&page=2'] as $uri) {
                ok(str_contains(page_with('', $main, $uri)->body, '<meta name="robots" content="noindex, follow">'), "$uri should be noindex");
            }
        } finally {
            ItTour\Config::override(['production' => false]);
        }
    },
    'lists: page links keep the filters and drop empty values' => function () use ($main) {
        $body = page_with('', $main, '/talks?q=&city=varna&year=&page=2')->body;
        ok(str_contains($body, 'href="' . e(ItTour\Site::mainUrl('/talks?city=varna&page=3')) . '"'), 'next page link');
        ok(str_contains($body, 'href="' . e(ItTour\Site::mainUrl('/talks?city=varna')) . '" rel="prev"'), 'previous page is page 1 without ?page');
    },
    'lists: a page past the end or a bad page number is a 404' => function () use ($main) {
        same(404, page_with('', $main, '/talks?page=999')->status);
        same(404, page_with('', $main, '/speakers?page=0')->status);
        same(200, page_with('', $main, '/talks?q=nothing-matches-this')->status);
    },
    'lists: unpublished talks and speakers are left out' => function () use ($main) {
        $res = page_with('UPDATE speakers SET published = 0 WHERE id = 15', $main, '/speakers?q=nakov');
        ok(!str_contains($res->body, 'Светлин Наков'), 'unpublished speaker listed');
        $res = page_with("UPDATE events SET schedule_published = 0 WHERE year = 2024", $main, '/talks?year=2024');
        ok(str_contains($res->body, 'Няма лекции'), 'talks of an unpublished programme listed');
    },
    'home: season sponsors, newsletter and the videos switch' => function () use ($main) {
        $sql = "INSERT INTO sponsorships (sponsor_id, year, type, sort_order) SELECT id, 2026, 'Спонсори', 1 FROM sponsors WHERE name = 'StorPool';
                UPDATE settings SET value = 'https://example.com/newsletter' WHERE key = 'newsletter_url';
                UPDATE settings SET value = '0' WHERE key = 'home_videos';";
        $body = page_with($sql, $main, '/')->body;
        ok(str_contains($body, 'id="sponsors"') && str_contains($body, 'Партньори 2026'), 'season sponsors missing');
        ok(str_contains($body, 'id="subscribe"') && str_contains($body, 'href="https://example.com/newsletter"'), 'newsletter missing');
        ok(!str_contains($body, 'id="videos"'), 'videos block should be off');
    },
    'home: an event without a map position is left off the map, not the list' => function () use ($main) {
        $body = page_with("UPDATE cities SET lat = NULL, lng = NULL WHERE slug = 'ruse'", $main, '/')->body;
        same(4, substr_count($body, '<g class="node'));
        ok(str_contains($body, '<h3 lang="en">RuseConf</h3>'), 'Ruse missing from the list');
    },
];
