<?php

declare(strict_types=1);

namespace ItTour;

use ItTour\Pages\Archive;
use ItTour\Pages\EventPage;
use ItTour\Pages\Home;
use ItTour\Pages\NotFound;
use ItTour\Pages\SpeakerPage;
use ItTour\Pages\SpeakersDirectory;
use ItTour\Pages\TalksLibrary;
use ItTour\Pages\TalkPage;

/**
 * Turns a request into a response: picks the site from the host, matches the
 * route, runs the handler and renders its page. Unknown addresses go through
 * the redirects table, then get the 404 page and are logged as missed.
 */
final class App
{
    /** Most missed addresses kept, so bots probing random paths can't grow the table forever. */
    private const MAX_MISSED = 10000;

    public static function routes(Site $site): Router
    {
        $r = new Router();
        if ($site->isMain()) {
            $r->add('/', fn () => Home::show());
            $r->add('/archive', fn ($req) => Archive::show($req));
            $r->add('/talks', fn ($req) => TalksLibrary::show($req));
            $r->add('/speakers', fn ($req) => SpeakersDirectory::show($req));
            $r->add('/talks/{id:\d+}-{slug:[a-z0-9-]*}', fn ($req, $p) => TalkPage::show($req, (int) $p['id'], $p['slug']));
            $r->add('/talks/{id:\d+}', fn ($req, $p) => TalkPage::show($req, (int) $p['id'], ''));
        } else {
            $r->add('/', fn () => EventPage::show($site));
            $r->add('/archive/{year:\d{4}}', fn ($req, $p) => EventPage::show($site, (int) $p['year']));
        }
        // Speaker profiles answer on every domain, as on the old sites.
        $r->add('/speakers/{id:\d+}', fn ($req, $p) => SpeakerPage::show($site, (int) $p['id']));
        return $r;
    }

    public static function handle(Request $req): Response
    {
        set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
            if (!(error_reporting() & $severity)) {
                return false;
            }
            throw new \ErrorException($message, 0, $severity, $file, $line);
        });
        Clock::fake(getenv('IT_TOUR_TESTING') === '1' ? $req->header('X-Test-Now') : null);
        try {
            $response = self::dispatch($req);
        } catch (\Throwable $e) {
            Log::error($e, $req);
            $response = new Response(500, Config::get('debug')
                ? '<pre>' . e((string) $e) . '</pre>'
                : View::render('error'));
        } finally {
            restore_error_handler();
        }
        return self::withHeaders($response, $req);
    }

    private static function dispatch(Request $req): Response
    {
        if (!in_array($req->method, ['GET', 'HEAD'], true)) {
            return new Response(405, 'Method not allowed', ['Content-Type' => 'text/plain; charset=utf-8', 'Allow' => 'GET, HEAD']);
        }
        // www.varnaconf.com → varnaconf.com (the server does this too, where it can).
        if (preg_match('/^www\.(.+)$/i', $req->host, $m)) {
            return Response::redirect(Config::get('scheme') . '://' . $m[1] . self::uri($req));
        }
        $site = Site::fromHost($req->host);
        if ($site === null) {
            return Response::text('Unknown site', 404);
        }

        $match = self::routes($site)->match($req->path);
        if ($match) {
            [$handler, $params] = $match;
            $result = $handler($req, $params);
            if ($result !== null) {
                return $result instanceof Response ? $result : View::page($result, $site, $req);
            }
            // The handler found nothing (no such edition, unpublished talk…): fall through to 404.
        }

        $redirect = Db::one(
            "SELECT target FROM redirects WHERE path = ? AND host IN ('', ?) ORDER BY host DESC LIMIT 1",
            [$req->path, $site->domain()],
        );
        if ($redirect) {
            return Response::redirect(str_starts_with($redirect['target'], '/') ? $site->url($redirect['target']) : $redirect['target']);
        }

        self::logMissed($site, $req);
        return View::page(NotFound::page(), $site, $req);
    }

    private static function logMissed(Site $site, Request $req): void
    {
        $host = $site->domain();
        $path = mb_substr($req->path, 0, 500);
        $updated = Db::run("UPDATE missed_urls SET hits = hits + 1, last_seen = datetime('now') WHERE host = ? AND path = ?", [$host, $path]);
        if (!$updated && Db::value('SELECT COUNT(*) FROM missed_urls') < self::MAX_MISSED) {
            Db::run('INSERT OR IGNORE INTO missed_urls (host, path) VALUES (?, ?)', [$host, $path]);
        }
    }

    private static function uri(Request $req): string
    {
        return $req->path . ($req->query ? '?' . http_build_query($req->query) : '');
    }

    /** Security headers on every response, and "noindex" outside production. */
    private static function withHeaders(Response $res, Request $req): Response
    {
        $res->headers += [
            'Content-Security-Policy' => implode('; ', [
                "default-src 'self'",
                "img-src 'self' data:",
                "style-src 'self'",
                "style-src-attr 'unsafe-inline'",   // per-element colours: style="--c:#6dc4aa"
                "script-src 'self'",
                'frame-src https://www.youtube-nocookie.com https://www.google.com https://maps.google.com',
                "frame-ancestors 'none'",
                "base-uri 'self'",
                "form-action 'self'",
            ]),
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
        ];
        if (!Config::get('production')) {
            $res->headers['X-Robots-Tag'] = 'noindex, nofollow';
        }
        return $res;
    }
}
