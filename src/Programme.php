<?php

declare(strict_types=1);

namespace ItTour;

/** An event's schedule, talks and speakers, with their photos. Published content only. */
final class Programme
{
    /**
     * Slots in time order. Talk slots carry 'talk' with its 'speakers'.
     * Slots whose talk isn't published are left out.
     */
    public static function slots(int $eventId): array
    {
        $talks = self::talks($eventId);
        $slots = [];
        foreach (Db::all('SELECT * FROM slots WHERE event_id = ? ORDER BY starts_at, id', [$eventId]) as $slot) {
            if ($slot['talk_id'] !== null) {
                if (!isset($talks[$slot['talk_id']])) {
                    continue;
                }
                $slot['talk'] = $talks[$slot['talk_id']];
            }
            $slots[] = $slot;
        }
        // Slots that start together run in parallel (workshops next to talks).
        $starts = array_count_values(array_column($slots, 'starts_at'));
        foreach ($slots as &$slot) {
            $slot['parallel'] = $starts[$slot['starts_at']] > 1;
        }
        return $slots;
    }

    /** Published talks of an event (id => talk), each with its published speakers in order. */
    public static function talks(int $eventId): array
    {
        $talks = [];
        foreach (Db::all('SELECT * FROM talks WHERE event_id = ? AND published = 1', [$eventId]) as $t) {
            $t['speakers'] = [];
            $talks[$t['id']] = $t;
        }
        if (!$talks) {
            return [];
        }
        $in = implode(',', array_keys($talks));
        $rows = Db::all(
            'SELECT ts.talk_id, s.*, ' . Media::JOIN_COLUMNS . "
             FROM talk_speakers ts
             JOIN speakers s ON s.id = ts.speaker_id AND s.published = 1
             LEFT JOIN media m ON m.id = s.photo_media_id
             WHERE ts.talk_id IN ($in)
             ORDER BY ts.talk_id, ts.sort_order"
        );
        foreach ($rows as $r) {
            $talks[$r['talk_id']]['speakers'][] = Media::pluck($r, 'photo');
        }
        return $talks;
    }

    /**
     * The event's speakers in programme order, each once, with the titles of
     * their talks at this event.
     */
    public static function speakers(array $slots): array
    {
        $out = [];
        foreach ($slots as $slot) {
            foreach ($slot['talk']['speakers'] ?? [] as $sp) {
                $out[$sp['id']] ??= $sp + ['talks' => []];
                $out[$sp['id']]['talks'][] = $slot['talk']['title'];
            }
        }
        return array_values($out);
    }

    /** The slot on stage at $now (the latest started slot that hasn't ended), or null. */
    public static function onStage(array $slots, string $now): ?array
    {
        $current = null;
        foreach ($slots as $slot) {
            if ($slot['starts_at'] <= $now && ($slot['ends_at'] === null || $now < $slot['ends_at'])) {
                $current = $slot;
            }
        }
        return $current;
    }
}
