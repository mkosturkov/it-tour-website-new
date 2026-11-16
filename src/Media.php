<?php

declare(strict_types=1);

namespace ItTour;

use GdImage;
use RuntimeException;

/**
 * The media library. Originals are kept untouched in data/media/; the public
 * copies are WebP files at a few widths in public/media/, named by a slug and
 * the start of the content hash: svetlin-nakov-3f9a1c2b-800.webp.
 * SVG files are checked for scripts and published as they are.
 */
final class Media
{
    public const WIDTHS = [400, 800, 1600];
    private const QUALITY = 80;

    public static function originalsDir(): string
    {
        return APP_ROOT . '/data/media';
    }

    public static function publicDir(): string
    {
        return APP_ROOT . '/public/media';
    }

    /**
     * Adds a file to the library and produces its public copies.
     * If the same content is already there, returns the existing id.
     *
     * $ratio: crop to this width/height ratio around the centre (1.0: square
 * speaker photos; 16/9: video stills), or null to keep the whole image.
     */
    public static function add(string $file, string $name, string $alt = '', ?float $ratio = null, ?string $source = null): int
    {
        $bytes = file_get_contents($file);
        $hash = sha1($bytes);
        if ($id = Db::value('SELECT id FROM media WHERE hash = ?', [$hash])) {
            return (int) $id;
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($bytes);
        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            default => throw new RuntimeException("Unsupported file type $mime: $file"),
        };
        $slug = Text::slug($name, 40);

        @mkdir(self::originalsDir(), 0777, true);
        @mkdir(self::publicDir(), 0777, true);
        file_put_contents(self::originalsDir() . "/$hash.$ext", $bytes);

        $row = [
            'hash' => $hash, 'name' => $slug, 'mime' => $mime, 'ext' => $ext,
            'alt' => $alt, 'source' => $source, 'width' => null, 'height' => null,
            'crop' => null, 'widths' => '[]',
        ];

        if ($ext === 'svg') {
            self::checkSvg($bytes, $file);
            file_put_contents(self::publicDir() . '/' . self::fileName($slug, $hash, null, 'svg'), $bytes);
        } else {
            $img = self::load($bytes, $mime);
            $w = imagesx($img);
            $h = imagesy($img);
            $row['width'] = $w;
            $row['height'] = $h;
            $crop = ['x' => 0, 'y' => 0, 'w' => $w, 'h' => $h];
            if ($ratio && abs($w / $h - $ratio) > 0.01) {
                // Centred crop to width/height = $ratio (1.0 for speaker photos, 16/9 for video stills).
                $cw = (int) min($w, round($h * $ratio));
                $ch = (int) min($h, round($w / $ratio));
                $crop = ['x' => intdiv($w - $cw, 2), 'y' => intdiv($h - $ch, 2), 'w' => $cw, 'h' => $ch];
                $row['crop'] = json_encode($crop);
            }
            $row['widths'] = json_encode(self::produce($img, $crop, $slug, $hash));
        }

        return Db::insert('media', $row);
    }

    public static function find(?int $id): ?array
    {
        return $id ? Db::one('SELECT * FROM media WHERE id = ?', [$id]) : null;
    }

    /** Columns to select when joining media as alias m: they come back as m_id, m_hash… */
    public const JOIN_COLUMNS = 'm.id AS m_id, m.hash AS m_hash, m.name AS m_name, m.ext AS m_ext,
        m.widths AS m_widths, m.crop AS m_crop, m.width AS m_width, m.height AS m_height, m.alt AS m_alt';

    /**
     * Moves the joined media columns (m_id, m_hash…) of a row into $row[$as]:
     * a media row, or null when there is none.
     */
    public static function pluck(array $row, string $as): array
    {
        $media = null;
        if (!empty($row['m_id'])) {
            $media = [];
            foreach (['id', 'hash', 'name', 'ext', 'widths', 'crop', 'width', 'height', 'alt'] as $k) {
                $media[$k] = $row["m_$k"];
            }
        }
        foreach (array_keys($row) as $k) {
            if (str_starts_with($k, 'm_')) {
                unset($row[$k]);
            }
        }
        $row[$as] = $media;
        return $row;
    }

    /** Public URL of a media item at the width closest to $want (or its SVG). */
    public static function url(array $media, int $want = 800): string
    {
        if ($media['ext'] === 'svg') {
            return '/media/' . self::fileName($media['name'], $media['hash'], null, 'svg');
        }
        $widths = json_decode($media['widths'], true) ?: [];
        $pick = end($widths);
        foreach ($widths as $w) {
            if ($w >= $want) {
                $pick = $w;
                break;
            }
        }
        return '/media/' . self::fileName($media['name'], $media['hash'], $pick, 'webp');
    }

    /** srcset attribute value for all produced widths. */
    public static function srcset(array $media): string
    {
        $widths = json_decode($media['widths'], true) ?: [];
        return implode(', ', array_map(
            fn ($w) => '/media/' . self::fileName($media['name'], $media['hash'], $w, 'webp') . " {$w}w",
            $widths,
        ));
    }

    /** Width and height of the public copy (after cropping) at width $w. */
    public static function size(array $media, int $w): array
    {
        $crop = $media['crop'] ? json_decode($media['crop'], true) : ['w' => $media['width'], 'h' => $media['height']];
        if (!$crop['w']) {
            return [null, null];
        }
        return [$w, (int) round($crop['h'] * $w / $crop['w'])];
    }

    private static function fileName(string $slug, string $hash, ?int $width, string $ext): string
    {
        return $slug . '-' . substr($hash, 0, 8) . ($width ? "-$width" : '') . ".$ext";
    }

    private static function load(string $bytes, string $mime): GdImage
    {
        $img = @imagecreatefromstring($bytes);
        if (!$img) {
            throw new RuntimeException('GD could not read the image');
        }
        if (!imageistruecolor($img)) {
            imagepalettetotruecolor($img);
        }
        // Phones store rotation in EXIF instead of rotating the pixels.
        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data('data://image/jpeg;base64,' . base64_encode($bytes));
            $rotate = match ((int) ($exif['Orientation'] ?? 1)) {
                3 => 180, 6 => -90, 8 => 90, default => 0,
            };
            if ($rotate) {
                $img = imagerotate($img, $rotate, 0);
            }
        }
        return $img;
    }

    /** Writes WebP copies at each width up to the cropped width. Returns the widths. */
    private static function produce(GdImage $img, array $crop, string $slug, string $hash): array
    {
        $widths = array_values(array_filter(self::WIDTHS, fn ($w) => $w <= $crop['w']));
        if (!$widths) {
            $widths = [$crop['w']];       // smaller than 400: one copy at its own size
        }
        foreach ($widths as $w) {
            $h = max(1, (int) round($crop['h'] * $w / $crop['w']));
            $out = imagecreatetruecolor($w, $h);
            imagealphablending($out, false);
            imagesavealpha($out, true);
            imagefill($out, 0, 0, imagecolorallocatealpha($out, 0, 0, 0, 127));
            imagecopyresampled($out, $img, 0, 0, $crop['x'], $crop['y'], $w, $h, $crop['w'], $crop['h']);
            imagewebp($out, self::publicDir() . '/' . self::fileName($slug, $hash, $w, 'webp'), self::QUALITY);
        }
        return $widths;
    }

    private static function checkSvg(string $bytes, string $file): void
    {
        if (preg_match('/<script|\bon[a-z]+\s*=|javascript:|<foreignObject/i', $bytes)) {
            throw new RuntimeException("SVG contains scripts or event handlers: $file");
        }
    }
}
