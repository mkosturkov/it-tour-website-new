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
            $content = self::render($page->template, $page->data);
            $body = self::render('layout', $page->data + ['content' => $content]);
        }
        return new Response($page->status, $body);
    }
}
