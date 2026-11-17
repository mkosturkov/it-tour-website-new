<?php

declare(strict_types=1);

namespace ItTour;

/**
 * Plain PHP templates in templates/. A page template is rendered first, then
 * wrapped in templates/layout.php, which receives it as $content.
 *
 * Template rules (bin/check enforces the first):
 * - print values only through e(), or partial() / raw() for finished HTML
 * - templates only display; queries belong in the page handlers
 */
final class View
{
    /** The site and request being rendered, for helpers such as url(). */
    public static Site $site;
    public static Request $request;

    public static function render(string $template, array $data = []): string
    {
        $file = APP_ROOT . "/templates/$template.php";
        if (!is_file($file)) {
            throw new \RuntimeException("No template $template");
        }
        $render = static function (string $__file, array $__data): string {
            extract($__data, EXTR_SKIP);
            ob_start();
            try {
                require $__file;
                return (string) ob_get_clean();
            } catch (\Throwable $e) {
                ob_end_clean();
                throw $e;
            }
        };
        return $render($file, $data);
    }

    /** Renders a page for this request: the full page in its layout, or only its results block. */
    public static function page(Page $page, Site $site, Request $req): Response
    {
        self::$site = $site;
        self::$request = $req;
        if ($page->partial && $req->wantsPartial()) {
            $body = self::render($page->partial, $page->data);
        } else {
            $data = $page->data;
            // The season pop-up on main-site pages; a page can turn it off with 'popup' => null.
            if ($site->isMain() && !array_key_exists('popup', $data)) {
                $data['popup'] = Season::popup();
            }
            $content = self::render($page->template, $data);
            $body = self::render('layout', $data + ['content' => $content]);
        }
        $res = new Response($page->status, self::externalLinks($body));
        if ($page->partial) {
            // The same address gives the whole page or only its results; caches must keep both.
            $res->headers['Vary'] = 'X-Partial';
        }
        return $res;
    }

    /**
     * Links to other websites (YouTube, Google Maps, speakers' and sponsors'
     * sites…) open in a new tab; links between the IT Tour sites don't.
     * Done here on the finished HTML, so no template can forget it.
     * Modern browsers imply rel="noopener" for target="_blank".
     */
    public static function externalLinks(string $html): string
    {
        return preg_replace_callback(
            '~<a\b[^>]*>~',
            static function (array $m): string {
                $tag = $m[0];
                if (str_contains($tag, ' target=') || !preg_match('~\shref="(https?://[^"/?#]+)~i', $tag, $h)) {
                    return $tag;
                }
                $host = (string) parse_url(html_entity_decode($h[1]), PHP_URL_HOST);
                $port = parse_url(html_entity_decode($h[1]), PHP_URL_PORT);
                return Site::fromHost($host . ($port ? ":$port" : '')) ? $tag : substr($tag, 0, -1) . ' target="_blank">';
            },
            $html,
        );
    }
}
