<?php

declare(strict_types=1);

namespace ItTour;

/**
 * Matches a path against patterns such as /archive/{year:\d{4}}.
 * A {name} without a rule matches one path segment.
 */
final class Router
{
    private array $routes = [];

    public function add(string $pattern, callable $handler): self
    {
        $regex = preg_replace_callback(
            '/\{(\w+)(?::((?:[^{}]|\{\d+(?:,\d*)?\})+))?\}/',
            fn ($m) => '(?P<' . $m[1] . '>' . ($m[2] ?? '[^/]+') . ')',
            $pattern,
        );
        $this->routes[] = ['#^' . $regex . '$#u', $handler];
        return $this;
    }

    /** @return array{0: callable, 1: array}|null the handler and the named parts */
    public function match(string $path): ?array
    {
        foreach ($this->routes as [$regex, $handler]) {
            if (preg_match($regex, $path, $m)) {
                return [$handler, array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY)];
            }
        }
        return null;
    }
}
