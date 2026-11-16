<?php

declare(strict_types=1);

namespace ItTour;

/**
 * Queries about talks for the talk pages, speaker profiles and (later) the
 * talks library. A talk is public only when the talk, its edition and that
 * edition's programme are all published.
 */
final class Talks
{
    public const VISIBLE = 't.published = 1 AND e.published = 1 AND e.schedule_published = 1';

    private const SELECT = 'SELECT t.*, e.name AS event_name, e.year AS event_year, e.date AS event_date,
            e.starts_at AS event_starts_at, e.ends_at AS event_ends_at, e.colour AS event_colour,
            e.venue_name AS event_venue, c.slug AS city_slug, c.name AS city_name, c.domain AS city_domain, '
        . Media::JOIN_COLUMNS . '
        FROM talks t
        JOIN events e ON e.id = t.event_id
        JOIN cities c ON c.id = e.city_id
        LEFT JOIN media m ON m.id = t.video_still_media_id';

    /** A public talk with its speakers, or null. */
    public static function find(int $id): ?array
    {
        $row = Db::one(self::SELECT . ' WHERE t.id = ? AND ' . self::VISIBLE, [$id]);
        if (!$row) {
            return null;
        }
        $talk = self::shape($row);
        $talk['speakers'] = self::speakers([$id])[$id] ?? [];
        return $talk;
    }

    /** A speaker's public talks, newest first, each with all its speakers. */
    public static function forSpeaker(int $speakerId): array
    {
        $rows = Db::all(
            self::SELECT . ' JOIN talk_speakers ts ON ts.talk_id = t.id AND ts.speaker_id = ?
             WHERE ' . self::VISIBLE . ' ORDER BY e.date DESC, t.id',
            [$speakerId],
        );
        $talks = array_map([self::class, 'shape'], $rows);
        $speakers = self::speakers(array_column($talks, 'id'));
        foreach ($talks as &$t) {
            $t['speakers'] = $speakers[$t['id']] ?? [];
        }
        return $talks;
    }

    /** Other public talks of the same edition, in programme order. */
    public static function sameEvent(int $eventId, int $exceptId): array
    {
        return Db::all(
            'SELECT t.id, t.title, t.slug FROM talks t JOIN events e ON e.id = t.event_id
             LEFT JOIN slots s ON s.talk_id = t.id
             WHERE t.event_id = ? AND t.id <> ? AND ' . self::VISIBLE . '
             ORDER BY s.starts_at, t.id',
            [$eventId, $exceptId],
        );
    }

    /** Published speakers of the given talks: talk id => [speaker with 'photo', ...] in order. */
    public static function speakers(array $talkIds): array
    {
        if (!$talkIds) {
            return [];
        }
        $in = implode(',', array_map('intval', $talkIds));
        $out = [];
        foreach (Db::all(
            'SELECT ts.talk_id, s.*, ' . Media::JOIN_COLUMNS . "
             FROM talk_speakers ts
             JOIN speakers s ON s.id = ts.speaker_id AND s.published = 1
             LEFT JOIN media m ON m.id = s.photo_media_id
             WHERE ts.talk_id IN ($in)
             ORDER BY ts.talk_id, ts.sort_order"
        ) as $r) {
            $out[$r['talk_id']][] = Media::pluck($r, 'photo');
        }
        return $out;
    }

    /** Event fields of a joined row grouped as 'event'; the still as 'still'. */
    private static function shape(array $row): array
    {
        $row = Media::pluck($row, 'still');
        $row['event'] = [
            'id' => $row['event_id'], 'name' => $row['event_name'], 'year' => $row['event_year'],
            'date' => $row['event_date'], 'starts_at' => $row['event_starts_at'], 'ends_at' => $row['event_ends_at'],
            'colour' => $row['event_colour'], 'venue_name' => $row['event_venue'],
            'city_slug' => $row['city_slug'], 'city_name' => $row['city_name'], 'city_domain' => $row['city_domain'],
        ];
        foreach (array_keys($row) as $k) {
            if (str_starts_with($k, 'event_') && $k !== 'event_id' || str_starts_with($k, 'city_')) {
                unset($row[$k]);
            }
        }
        return $row;
    }
}
