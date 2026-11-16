<?php

declare(strict_types=1);

namespace ItTour;

/**
 * schema.org data for search engines, printed as JSON-LD in the page's <head>
 * (decisions.md, "Machine-readable"). Each builder describes only what the
 * page itself shows.
 */
final class Schema
{
    public static function organization(): array
    {
        return [
            '@type' => 'Organization',
            '@id' => Site::mainUrl('/#organization'),
            'name' => 'IT Tour',
            'url' => Site::mainUrl('/'),
            'logo' => Site::mainUrl('/assets/img/it-tour.svg'),
            'sameAs' => array_column(Settings::json('social_links'), 'url'),
        ];
    }

    /** "IT Tour › Лекции › <title>": [[name, url], ...]; the last item is the page itself. */
    public static function breadcrumbs(array $items): array
    {
        $list = [];
        foreach (array_values($items) as $i => [$name, $url]) {
            $list[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $name, 'item' => $url];
        }
        return ['@type' => 'BreadcrumbList', 'itemListElement' => $list];
    }

    /** An edition as an Event, with its speakers as performers. */
    public static function event(array $ev, array $speakers, ?array $image, string $url): array
    {
        $data = [
            '@type' => 'Event',
            'name' => $ev['name'],
            'url' => $url,
            'startDate' => Events::startsAt($ev)->format('c'),
            'endDate' => Events::endsAt($ev)->format('c'),
            'eventStatus' => 'https://schema.org/EventScheduled',
            'eventAttendanceMode' => $ev['live_url'] || ($ev['stream_url'] ?? null)
                ? 'https://schema.org/MixedEventAttendanceMode'
                : 'https://schema.org/OfflineEventAttendanceMode',
            'isAccessibleForFree' => true,
            'inLanguage' => 'bg',
            'organizer' => ['@id' => Site::mainUrl('/#organization'), '@type' => 'Organization', 'name' => 'IT Tour', 'url' => Site::mainUrl('/')],
            'location' => array_filter([
                '@type' => 'Place',
                'name' => $ev['venue_name'] ?: $ev['city_name'],
                'address' => array_filter([
                    '@type' => 'PostalAddress',
                    'streetAddress' => $ev['venue_address'] ?: null,
                    'addressLocality' => $ev['city_name'],
                    'addressCountry' => 'BG',
                ]),
                'geo' => $ev['venue_lat'] !== null
                    ? ['@type' => 'GeoCoordinates', 'latitude' => (float) $ev['venue_lat'], 'longitude' => (float) $ev['venue_lng']]
                    : null,
            ]),
            'offers' => ['@type' => 'Offer', 'price' => 0, 'priceCurrency' => 'EUR', 'availability' => 'https://schema.org/InStock', 'url' => $url],
        ];
        if ($image) {
            $data['image'] = Site::mainUrl(Media::url($image, 1200));
        }
        if ($speakers) {
            $data['performer'] = array_map(fn ($s) => [
                '@type' => 'Person',
                'name' => $s['name'],
                'url' => Site::mainUrl('/speakers/' . $s['id']),
            ], $speakers);
        }
        return $data;
    }

    /** A speaker's profile page with the person as its main entity. */
    public static function profile(array $speaker, string $url): array
    {
        return [
            '@type' => 'ProfilePage',
            'url' => $url,
            'mainEntity' => array_filter([
                '@type' => 'Person',
                'name' => $speaker['name'],
                'url' => $url,
                'image' => $speaker['photo'] ? Site::mainUrl(Media::url($speaker['photo'], 800)) : null,
                'description' => $speaker['bio'] ? excerpt($speaker['bio'], 300) : null,
                'jobTitle' => $speaker['role'] ?: null,
                'worksFor' => $speaker['company'] ? ['@type' => 'Organization', 'name' => $speaker['company']] : null,
                'sameAs' => $speaker['links'] ?: null,
            ]),
        ];
    }

    /**
     * A talk's YouTube recording as a VideoObject. The upload date isn't known,
     * so the edition's date is used (the page shows it). Null without a still.
     */
    public static function video(array $talk, string $url): ?array
    {
        if (!$talk['still'] || !preg_match('~(?:youtu\.be/|[?&]v=|/embed/|/live/)([\w-]{11})~', (string) $talk['video_url'], $m)) {
            return null;
        }
        $start = (int) ($talk['video_start'] ?? 0);
        return array_filter([
            '@type' => 'VideoObject',
            'name' => $talk['title'],
            'description' => $talk['description'] ? excerpt($talk['description'], 300) : sprintf('%s, %s', $talk['title'], $talk['event']['name']),
            'thumbnailUrl' => Site::mainUrl(Media::url($talk['still'], 1200)),
            'uploadDate' => $talk['event']['date'],
            'embedUrl' => 'https://www.youtube-nocookie.com/embed/' . $m[1] . ($start ? "?start=$start" : ''),
            'url' => $url,
            'inLanguage' => lang_attr($talk['title']) ? 'en' : 'bg',
        ]);
    }

    /** JSON for a <script type="application/ld+json"> block: "<" escaped so text can't end the script. */
    public static function json(array $items): string
    {
        $graph = count($items) === 1 ? ['@context' => 'https://schema.org'] + $items[0] : ['@context' => 'https://schema.org', '@graph' => $items];
        return json_encode($graph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_THROW_ON_ERROR);
    }
}
