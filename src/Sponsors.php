<?php

declare(strict_types=1);

namespace ItTour;

/** Queries about sponsors. */
final class Sponsors
{
    /**
     * An event's published sponsors grouped by sponsorship type, groups in the
     * order of their first sponsor: [['type' => 'Златни', 'sponsors' => [...]], ...]
     */
    public static function forEvent(int $eventId): array
    {
        $rows = Db::all(
            'SELECT sp.type, s.*, ' . Media::JOIN_COLUMNS . '
             FROM sponsorships sp
             JOIN sponsors s ON s.id = sp.sponsor_id AND s.published = 1
             LEFT JOIN media m ON m.id = s.logo_media_id
             WHERE sp.event_id = ?
             ORDER BY sp.sort_order',
            [$eventId],
        );
        $groups = [];
        foreach ($rows as $r) {
            $type = trim($r['type']);
            $groups[$type] ??= ['type' => $type, 'sponsors' => []];
            $groups[$type]['sponsors'][] = Media::pluck($r, 'logo');
        }
        return array_values($groups);
    }
}
