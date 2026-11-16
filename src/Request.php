<?php

declare(strict_types=1);

namespace ItTour;

/** An incoming request: only what the site needs. */
final class Request
{
    public function __construct(
        public readonly string $method,
        public readonly string $host,
        public readonly string $path,
        public readonly array $query = [],
        public readonly array $headers = [],
    ) {
    }

    public static function fromGlobals(): self
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $headers = [];
        foreach ($_SERVER as $k => $v) {
            if (str_starts_with($k, 'HTTP_')) {
                $headers[strtolower(str_replace('_', '-', substr($k, 5)))] = (string) $v;
            }
        }
        return new self(
            strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            (string) ($_SERVER['HTTP_HOST'] ?? ''),
            rawurldecode((string) parse_url($uri, PHP_URL_PATH)) ?: '/',
            $_GET,
            $headers,
        );
    }

    /** For tests: GET http://$host$uri */
    public static function get(string $host, string $uri, array $headers = []): self
    {
        parse_str((string) parse_url($uri, PHP_URL_QUERY), $query);
        return new self('GET', $host, rawurldecode((string) parse_url($uri, PHP_URL_PATH)) ?: '/', $query, array_change_key_case($headers));
    }

    public function header(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }

    /** A query value as a string ('' if missing or not a string). */
    public function q(string $key): string
    {
        $v = $this->query[$key] ?? '';
        return is_string($v) ? trim($v) : '';
    }

    /** The script asks for just the results block (instant search). */
    public function wantsPartial(): bool
    {
        return $this->header('X-Partial') === 'results';
    }
}
