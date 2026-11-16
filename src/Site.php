<?php

declare(strict_types=1);

namespace ItTour;

/**
 * Which site a request is for (the main site or a city site), and links
 * between sites. Hosts come from config.php, so a demo or a local copy
 * never links to the real domains.
 */
final class Site
{
    /** @param array|null $city the cities row, or null for the main site */
    private function __construct(public readonly ?array $city)
    {
    }

    public static function main(): self
    {
        return new self(null);
    }

    public static function forCity(array $city): self
    {
        return new self($city);
    }

    public function isMain(): bool
    {
        return $this->city === null;
    }

    /**
     * The site for a request's Host header, or null if it isn't one of ours.
     * Host matching ignores letter case and a leading "www.".
     */
    public static function fromHost(string $host): ?self
    {
        $host = strtolower(preg_replace('/^www\./i', '', $host));
        if ($host === strtolower(Config::get('main_host'))) {
            return self::main();
        }
        foreach (Cities::all() as $city) {
            if ($host === strtolower(self::cityHost($city))) {
                return self::forCity($city);
            }
        }
        return null;
    }

    /** "varnaconf.com" → "varnaconf" */
    public static function cityKey(array $city): string
    {
        return preg_replace('/\.[a-z]+$/i', '', $city['domain']);
    }

    public static function cityHost(array $city): string
    {
        $pattern = Config::get('city_host');
        return $pattern ? str_replace('{city}', self::cityKey($city), $pattern) : $city['domain'];
    }

    /** The site's production domain, used as its key in tables: it-tour.bg or varnaconf.com. */
    public function domain(): string
    {
        return $this->city['domain'] ?? 'it-tour.bg';
    }

    public function host(): string
    {
        return $this->city ? self::cityHost($this->city) : Config::get('main_host');
    }

    /** Absolute URL on this site. */
    public function url(string $path = '/'): string
    {
        return Config::get('scheme') . '://' . $this->host() . $path;
    }

    /** Absolute URL on the main site. */
    public static function mainUrl(string $path = '/'): string
    {
        return self::main()->url($path);
    }

    /** Absolute URL on a city's site. */
    public static function cityUrl(array $city, string $path = '/'): string
    {
        return self::forCity($city)->url($path);
    }
}
