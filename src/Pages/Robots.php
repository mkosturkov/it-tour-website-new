<?php

declare(strict_types=1);

namespace ItTour\Pages;

use ItTour\Config;
use ItTour\Db;
use ItTour\Events;
use ItTour\Response;
use ItTour\Site;
use ItTour\Talks;
use ItTour\Talks as T;
use ItTour\View;

/**
 * Files for crawlers, generated on every domain (decisions.md, "robots.txt and
 * sitemap.xml on every domain", "llms.txt"):
 *  - robots.txt: everything allowed in production, with the sitemap; outside
 *    production everything is disallowed
 *  - sitemap.xml: the domain's canonical pages
 *  - llms.txt (it-tour.bg only): a Markdown guide to the site for AI tools
 */
final class Robots
{
    public static function robots(Site $site): Response
    {
        $body = Config::get('production')
            ? "User-agent: *\nAllow: /\n\nSitemap: " . $site->url('/sitemap.xml') . "\n"
            : "# Not the live site: nothing here should be indexed.\nUser-agent: *\nDisallow: /\n";
        return Response::text($body);
    }

    public static function sitemap(Site $site): Response
    {
        $urls = [];
        if ($site->isMain()) {
            foreach (['/', '/archive', '/talks', '/speakers', '/team'] as $path) {
                $urls[] = $site->url($path);
            }
            foreach (Db::all('SELECT t.id, t.slug FROM talks t JOIN events e ON e.id = t.event_id WHERE ' . T::VISIBLE . ' ORDER BY t.id') as $t) {
                $urls[] = talk_url($t);
            }
            // Speakers with a public talk; their profiles' canonical address is on it-tour.bg.
            foreach (Db::all(
                'SELECT DISTINCT s.id FROM speakers s JOIN talk_speakers ts ON ts.speaker_id = s.id
                 JOIN talks t ON t.id = ts.talk_id JOIN events e ON e.id = t.event_id
                 WHERE s.published = 1 AND ' . T::VISIBLE . ' ORDER BY s.id'
            ) as $s) {
                $urls[] = $site->url('/speakers/' . $s['id']);
            }
        } else {
            // "/" is the latest edition; older ones live at /archive/<year>.
            $urls[] = $site->url('/');
            foreach (array_slice(Events::years((int) $site->city['id']), 1) as $year) {
                $urls[] = $site->url('/archive/' . $year);
            }
        }
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
        foreach ($urls as $u) {
            $xml .= '  <url><loc>' . htmlspecialchars($u, ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc></url>\n";
        }
        return Response::text($xml . "</urlset>\n", 200, 'application/xml');
    }

    public static function llms(Site $site, \ItTour\Request $req): Response
    {
        View::$site = $site;
        View::$request = $req;
        return Response::text(View::render('llms', [
            'season' => \ItTour\Season::current(),
            'counts' => Talks::counts(),
            'years' => Events::allYears(),
            'editions' => count(Events::archive()),
        ]), 200, 'text/markdown');
    }
}
