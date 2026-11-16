<?php

declare(strict_types=1);

namespace ItTour;

/**
 * Error log: one file per day in data/logs/, kept 30 days. A day's file stops
 * taking details at 10 MB and gets a single "log full" line instead, so a loop
 * of errors can't fill the disk.
 */
final class Log
{
    public const MAX_BYTES = 10 * 1024 * 1024;
    public const KEEP_DAYS = 30;

    public static function dir(): string
    {
        return APP_ROOT . '/data/logs';
    }

    public static function error(\Throwable|string $error, ?Request $req = null): void
    {
        $dir = self::dir();
        @mkdir($dir, 0777, true);
        $now = Clock::now();
        $file = $dir . '/' . $now->format('Y-m-d') . '.log';
        $size = is_file($file) ? filesize($file) : 0;

        if ($size >= self::MAX_BYTES) {
            $full = "$file.full";
            if (!is_file($full)) {
                touch($full);
                file_put_contents($file, $now->format('c') . " LOG FULL: further errors today are not recorded\n", FILE_APPEND | LOCK_EX);
            }
            return;
        }

        if ($error instanceof \Throwable) {
            $line = sprintf(
                '%s %s: %s at %s:%d',
                $now->format('c'),
                $error::class,
                $error->getMessage(),
                str_replace(APP_ROOT . '/', '', $error->getFile()),
                $error->getLine(),
            );
            $detail = $error->getTraceAsString();
        } else {
            $line = $now->format('c') . ' ' . $error;
            $detail = '';
        }
        if ($req) {
            $line .= " [{$req->method} {$req->host}{$req->path}]";
        }
        file_put_contents($file, $line . "\n" . ($detail ? preg_replace('/^/m', '    ', $detail) . "\n" : ''), FILE_APPEND | LOCK_EX);

        // Now and then, delete old files.
        if (random_int(1, 100) === 1) {
            foreach (glob("$dir/*.log*") ?: [] as $f) {
                if (filemtime($f) < time() - self::KEEP_DAYS * 86400) {
                    @unlink($f);
                }
            }
        }
    }
}
