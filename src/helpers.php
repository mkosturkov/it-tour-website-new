<?php
/**
 * Small global helpers for templates. Kept as plain functions so templates
 * stay short: <?= e($talk['title']) ?>, <a href="<?= e(main_url('/talks')) ?>">.
 */

declare(strict_types=1);

use ItTour\Media;
use ItTour\Site;
use ItTour\View;

/** Escapes text for HTML, both in content and in attribute values. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
}

/** Marks finished HTML that must be printed as it is (rendered templates, JSON-LD). */
function raw(string $html): string
{
    return $html;
}

/** Renders templates/partials/$name.php. */
function partial(string $name, array $data = []): string
{
    return View::render("partials/$name", $data);
}

/** Absolute URL on the main site. */
function main_url(string $path = '/'): string
{
    return Site::mainUrl($path);
}

/** Absolute URL on a city's site. */
function city_url(array $city, string $path = '/'): string
{
    return Site::cityUrl($city, $path);
}

/** Absolute URL on the site being rendered. */
function here_url(string $path = '/'): string
{
    return View::$site->url($path);
}

/** Address of a file in public/assets/, with its modification time so browsers fetch changes. */
function asset(string $path): string
{
    $file = APP_ROOT . '/public/assets/' . $path;
    return '/assets/' . $path . (is_file($file) ? '?v=' . filemtime($file) : '');
}

/** Media library image URL at about $width pixels wide. */
function media_url(array $media, int $width = 800): string
{
    return Media::url($media, $width);
}

const BG_MONTHS = [1 => 'януари', 'февруари', 'март', 'април', 'май', 'юни', 'юли', 'август', 'септември', 'октомври', 'ноември', 'декември'];
const BG_WEEKDAYS = [1 => 'понеделник', 'вторник', 'сряда', 'четвъртък', 'петък', 'събота', 'неделя'];

/**
 * Bulgarian dates from 'YYYY-MM-DD':
 *   'long'  → събота, 22 август 2026
 *   'date'  → 22 август 2026
 *   'short' → 22 август
 *   'num'   → 22.08
 */
function date_bg(string $date, string $style = 'date'): string
{
    $d = new DateTimeImmutable($date, ItTour\Clock::zone());
    $day = (int) $d->format('j');
    $month = BG_MONTHS[(int) $d->format('n')];
    return match ($style) {
        'long' => BG_WEEKDAYS[(int) $d->format('N')] . ", $day $month " . $d->format('Y'),
        'short' => "$day $month",
        'num' => $d->format('d.m'),
        default => "$day $month " . $d->format('Y'),
    };
}

/**
 * Text colour for use on a background colour: near-black or white, whichever
 * contrasts more (WCAG relative luminance).
 */
function ink_for(string $hex): string
{
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    if (!preg_match('/^[0-9a-f]{6}$/i', $hex)) {
        return '#ffffff';
    }
    $lum = 0;
    foreach ([0.2126, 0.7152, 0.0722] as $i => $weight) {
        $c = hexdec(substr($hex, $i * 2, 2)) / 255;
        $lum += $weight * ($c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4);
    }
    $dark = 0.0137;     // luminance of #1d1d1d
    return (1.05) / ($lum + 0.05) >= ($lum + 0.05) / ($dark + 0.05) ? '#ffffff' : '#1d1d1d';
}

/** A safe CSS colour value, or the fallback. */
function css_colour(?string $hex, string $fallback = '#375992'): string
{
    return $hex && preg_match('/^#[0-9a-f]{3}([0-9a-f]{3})?$/i', $hex) ? strtolower($hex) : $fallback;
}
