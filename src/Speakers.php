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
