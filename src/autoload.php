<?php
/**
 * Loads our classes from src/: ItTour\Media is src/Media.php,
 * ItTour\Admin\Users would be src/Admin/Users.php.
 */

declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));

spl_autoload_register(function (string $class): void {
    if (!str_starts_with($class, 'ItTour\\')) {
        return;
    }
    $file = __DIR__ . '/' . str_replace('\\', '/', substr($class, 7)) . '.php';
    if (is_file($file)) {
        require $file;
    }
});
