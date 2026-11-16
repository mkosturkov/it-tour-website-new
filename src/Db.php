<?php

declare(strict_types=1);

namespace ItTour;

use PDO;

/** The SQLite database in data/site.sqlite. */
final class Db
{
    private static ?PDO $pdo = null;

    /** data/site.sqlite, or the file in IT_TOUR_DB (the browser tests use a frozen copy). */
    public static function path(): string
    {
        return getenv('IT_TOUR_DB') ?: APP_ROOT . '/data/site.sqlite';
    }

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            @mkdir(dirname(self::path()), 0777, true);
            self::$pdo = new PDO('sqlite:' . self::path(), null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            self::$pdo->exec('PRAGMA foreign_keys = ON; PRAGMA journal_mode = WAL; PRAGMA busy_timeout = 5000;');
        }
        return self::$pdo;
    }

    /** Closes the connection, for example before deleting the file. */
    public static function close(): void
    {
        self::$pdo = null;
    }

    /** Runs a query and returns all rows. */
    public static function all(string $sql, array $params = []): array
    {
        $st = self::pdo()->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    /** Runs a query and returns the first row, or null. */
    public static function one(string $sql, array $params = []): ?array
    {
        $st = self::pdo()->prepare($sql);
        $st->execute($params);
        return $st->fetch() ?: null;
    }

    /** Runs a query and returns the first column of the first row. */
    public static function value(string $sql, array $params = []): mixed
    {
        $st = self::pdo()->prepare($sql);
        $st->execute($params);
        $v = $st->fetchColumn();
        return $v === false ? null : $v;
    }

    public static function run(string $sql, array $params = []): int
    {
        $st = self::pdo()->prepare($sql);
        $st->execute($params);
        return $st->rowCount();
    }

    /** Inserts a row and returns its id. */
    public static function insert(string $table, array $row): int
    {
        $cols = array_keys($row);
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(', ', $cols),
            implode(', ', array_map(fn ($c) => ":$c", $cols)),
        );
        self::run($sql, $row);
        return (int) self::pdo()->lastInsertId();
    }

    /**
     * Applies migrations/NNN_name.sql files that haven't run yet, in order.
     * Returns the names of the files applied.
     */
    public static function migrate(): array
    {
        $pdo = self::pdo();
        $pdo->exec('CREATE TABLE IF NOT EXISTS schema_migrations (name TEXT PRIMARY KEY, applied_at TEXT NOT NULL DEFAULT (datetime(\'now\')))');
        $done = array_column(self::all('SELECT name FROM schema_migrations'), 'name');
        $files = glob(APP_ROOT . '/migrations/*.sql');
        sort($files);
        $applied = [];
        foreach ($files as $file) {
            $name = basename($file);
            if (in_array($name, $done, true)) {
                continue;
            }
            $pdo->beginTransaction();
            $pdo->exec(file_get_contents($file));
            self::run('INSERT INTO schema_migrations (name) VALUES (?)', [$name]);
            $pdo->commit();
            $applied[] = $name;
        }
        return $applied;
    }
}
