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
function page_with(string $sql, string $host, string $uri): ItTour\Response
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
        return App::handle(Request::get($host, $uri));
    } finally {
        Db::close();
        putenv('IT_TOUR_DB');
        @unlink($copy);
    }
}

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
    'text from the database is escaped' => function () use ($varna) {
        $res = page_with("UPDATE talks SET title = '<script>alert(1)</script>' WHERE id = (SELECT talk_id FROM slots s JOIN events e ON e.id = s.event_id JOIN cities c ON c.id = e.city_id WHERE e.year = 2024 AND c.slug = 'varna' AND talk_id IS NOT NULL LIMIT 1)", $varna, '/archive/2024');
        ok(!str_contains($res->body, '<script>alert(1)'), 'not escaped');
        ok(str_contains($res->body, '&lt;script&gt;alert(1)'), 'escaped title missing');
    },
];
