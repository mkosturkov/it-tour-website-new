<?php

declare(strict_types=1);

namespace ItTour;

/** Editor settings from the settings table (key → value, JSON where structured). */
final class Settings
{
    private static ?array $values = null;

    public static function get(string $key, mixed $default = null): mixed
    {
        self::$values ??= array_column(Db::all('SELECT key, value FROM settings'), 'value', 'key');
        return self::$values[$key] ?? $default;
    }

    public static function json(string $key): array
    {
        return json_decode((string) self::get($key, '[]'), true) ?: [];
    }

    public static function on(string $key): bool
    {
        return (bool) self::get($key, '0');
    }

    public static function reset(): void
    {
        self::$values = null;
    }
}
