<?php
/** The only entry point: every address that isn't a real file comes here. */

declare(strict_types=1);

// PHP's built-in server (bin/serve): let it serve real files itself.
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . rawurldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
    if ($file !== __DIR__ . '/' && is_file($file)) {
        return false;
    }
}

require dirname(__DIR__) . '/src/autoload.php';

$request = ItTour\Request::fromGlobals();
ItTour\App::handle($request)->send($request->method === 'HEAD');
