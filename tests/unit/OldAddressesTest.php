<?php
/**
 * Every address of the old sites keeps working (decisions.md, "Old addresses").
 * The list is tests/old-addresses.txt, taken from the November 2026 download.
 */

use ItTour\App;
use ItTour\Db;
use ItTour\Request;
use ItTour\Site;

if (!is_file(APP_ROOT . '/data/test/site.sqlite')) {
    return [];
}

/** The local host standing for an old domain: it-tour.bg → main_host, varnaconf.com → its city host. */
function local_host(string $domain): string
{
    return $domain === 'it-tour.bg' ? ItTour\Config::get('main_host') : Site::cityHost(['domain' => $domain]);
}

return [
    'every old page, slide file and imported image still works' => function () {
        // A copy: a broken address would be logged as missed, changing the frozen database.
        $copy = sys_get_temp_dir() . '/it-tour-old-addresses-' . getmypid() . '.sqlite';
        copy(APP_ROOT . '/data/test/site.sqlite', $copy);
        putenv("IT_TOUR_DB=$copy");
        Db::close();
        $broken = [];
        try {
            foreach (file(APP_ROOT . '/tests/old-addresses.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                if ($line[0] === '#') {
                    continue;
                }
                [$kind, $address] = explode("\t", $line);
                [$domain, $path] = explode('/', $address, 2);
                $res = App::handle(Request::get(local_host($domain), '/' . str_replace('%2F', '/', rawurlencode($path))));
                if ($kind === 'page' && $res->status !== 200) {
                    $broken[] = "$address: $res->status";
                }
                if ($kind === 'file' || $kind === 'image') {
                    $target = (string) parse_url($res->headers['Location'] ?? '', PHP_URL_PATH);
                    if ($res->status !== 301 || !is_file(APP_ROOT . '/public' . $target)) {
                        $broken[] = "$address: $res->status → " . ($res->headers['Location'] ?? 'nowhere');
                    }
                }
            }
        } finally {
            Db::close();
            putenv('IT_TOUR_DB');
            @unlink($copy);
        }
        same([], $broken);
    },
    'old images we don\'t have are a 404 and are not logged as missed' => function () {
        $copy = sys_get_temp_dir() . '/it-tour-old-images-' . getmypid() . '.sqlite';
        copy(APP_ROOT . '/data/test/site.sqlite', $copy);
        putenv("IT_TOUR_DB=$copy");
        Db::close();
        try {
            $host = local_host('varnaconf.com');
            same(404, App::handle(Request::get($host, '/system/uploads/photo/asset/15/original_image.JPG'))->status);
            same(404, App::handle(Request::get($host, '/system/uploads/../../config.php'))->status);
            same(0, (int) Db::value("SELECT COUNT(*) FROM missed_urls WHERE path LIKE '/system/uploads/%'"));
            App::handle(Request::get($host, '/no-such-page'));
            same(1, (int) Db::value("SELECT COUNT(*) FROM missed_urls WHERE path = '/no-such-page'"));
        } finally {
            Db::close();
            putenv('IT_TOUR_DB');
            @unlink($copy);
        }
    },
];
