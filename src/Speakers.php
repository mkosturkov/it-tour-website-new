<?php

declare(strict_types=1);

namespace ItTour;

/** Queries about speakers. */
final class Speakers
{
    /** A published speaker with 'photo' and decoded 'links', or null. */
    public static function find(int $id): ?array
    {
        $row = Db::one(
            'SELECT s.*, ' . Media::JOIN_COLUMNS . '
             FROM speakers s LEFT JOIN media m ON m.id = s.photo_media_id
             WHERE s.id = ? AND s.published = 1',
            [$id],
        );
        if (!$row) {
            return null;
        }
        $row = Media::pluck($row, 'photo');
        $row['links'] = json_decode($row['links'], true) ?: [];
        return $row;
    }

    /**
     * The speaker directory: published speakers with at least one public talk,
     * alphabetical. Returns [total, speakers on this page]; each has
     * 'photo', 'talk_count', 'cities' (names in season order) and 'latest'
     * (name and colour of their latest edition).
     * $f: city (slug: spoke in that city), year (spoke that year), q (search by name)
     */
    public static function directory(array $f, int $offset, int $limit): array
    {
        [$search, $params] = Text::searchWhere('s.search_text', (string) ($f['q'] ?? ''));
        $having = [];
        if (!empty($f['city'])) {
            $having[] = 'SUM(c.slug = ?) > 0';
            $params[] = $f['city'];
        }
        if (!empty($f['year'])) {
            $having[] = 'SUM(e.year = ?) > 0';
            $params[] = (int) $f['year'];
        }
        $having = $having ? 'HAVING ' . implode(' AND ', $having) : '';
        $sql = 'SELECT s.id, s.name, ' . Media::JOIN_COLUMNS . ', COUNT(DISTINCT t.id) AS talk_count
            FROM speakers s
            JOIN talk_speakers ts ON ts.speaker_id = s.id
            JOIN talks t ON t.id = ts.talk_id
            JOIN events e ON e.id = t.event_id
            JOIN cities c ON c.id = e.city_id
            LEFT JOIN media m ON m.id = s.photo_media_id
            WHERE s.published = 1 AND ' . Talks::VISIBLE . " AND $search
            GROUP BY s.id $having";
        $total = (int) Db::value("SELECT COUNT(*) FROM ($sql)", $params);
        $rows = Db::all("$sql ORDER BY s.name, s.id LIMIT ? OFFSET ?", [...$params, $limit, $offset]);
        if (!$rows) {
            return [$total, []];
        }

        // Cities and latest edition of the speakers on this page.
        $in = implode(',', array_column($rows, 'id'));
        $editions = Db::all(
            'SELECT DISTINCT ts.speaker_id, e.name, e.date, e.colour, c.name AS city, c.sort_order
             FROM talk_speakers ts
             JOIN talks t ON t.id = ts.talk_id
             JOIN events e ON e.id = t.event_id
             JOIN cities c ON c.id = e.city_id
             WHERE ts.speaker_id IN (' . $in . ') AND ' . Talks::VISIBLE . '
             ORDER BY e.date DESC'
        );
        $bySpeaker = [];
        foreach ($editions as $ed) {
            $bySpeaker[$ed['speaker_id']][] = $ed;
        }
        $speakers = [];
        foreach ($rows as $r) {
            $r = Media::pluck($r, 'photo');
            $eds = $bySpeaker[$r['id']];
            $r['latest'] = $eds[0];
            $cities = array_column($eds, 'city', 'sort_order');
            ksort($cities);
            $r['cities'] = array_values($cities);
            $speakers[] = $r;
        }
        return [$total, $speakers];
    }

    /** A readable label for a speaker's link: "LinkedIn", "GitHub", or the site's domain. */
    public static function linkLabel(string $url): string
    {
        $host = preg_replace('/^www\./', '', strtolower((string) parse_url($url, PHP_URL_HOST)));
        return match (true) {
            str_ends_with($host, 'linkedin.com') => 'LinkedIn',
            $host === 'github.com' => 'GitHub',
            $host === 'gitlab.com' => 'GitLab',
            $host === 'twitter.com', $host === 'x.com' => 'X',
            str_ends_with($host, 'facebook.com') => 'Facebook',
            str_ends_with($host, 'youtube.com') => 'YouTube',
            $host === 'instagram.com' => 'Instagram',
            str_ends_with($host, 'medium.com') => 'Medium',
            $host === 'speakerdeck.com' => 'Speaker Deck',
            $host === 'stackoverflow.com' => 'Stack Overflow',
            default => $host ?: $url,
        };
    }
}
