<?php

declare(strict_types=1);

namespace ItTour;

/** Settings of this installation, from config.php (see config.example.php). */
final class Config
{
    private static ?array $values = null;

    public static function get(string $key): mixed
    {
        if (self::$values === null) {
            // IT_TOUR_CONFIG points to another settings file (the browser tests use tests/config.php).
            $file = getenv('IT_TOUR_CONFIG') ?: APP_ROOT . '/config.php';
            self::$values = is_file($file) ? require $file : require APP_ROOT . '/config.example.php';
        }
        return self::$values[$key] ?? null;
    }

    /** Replaces settings, for tests. */
    public static function override(array $values): void
    {
        self::get('production');
        self::$values = $values + self::$values;
    }
}
