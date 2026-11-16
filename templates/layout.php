<?php
/**
 * Every page's frame: <head>, header, the page itself ($content) and footer.
 *
 * Page data used here (all optional except title):
 *   $title        page title; " · IT Tour" is added unless it already says IT Tour
 *   $description  meta description
 *   $nav          key of the active menu item (stops, talks, speakers, archive, team)
 *   $canonical    absolute canonical URL
 *   $noindex      true: search engines may follow the links but not index the page
 *   $colour       colour for this page on a city site (an edition's own colour);
 *                 defaults to the city's latest colour
 */
use ItTour\Cities;
use ItTour\Config;
use ItTour\Settings;
use ItTour\View;

$site = View::$site;
$fullTitle = str_contains($title, 'IT Tour') || str_contains($title, 'Conf') ? $title : "$title · IT Tour";
$city = null;
if (!$site->isMain()) {
    foreach (Cities::all() as $c) {
        if ($c['id'] === $site->city['id']) {
            $city = $c;
        }
    }
}
$colour = css_colour($colour ?? $city['colour'] ?? null, css_colour(Settings::get('accent')));
$style = ($city
    ? '--c:' . $colour . ';--c-ink:' . ink_for($colour)
    : '--accent:' . $colour . ';--accent-ink:' . ink_for($colour));
$classes = $city ? 'city' . (ink_for($colour) !== '#ffffff' ? ' ink-dark' : '') : 'hub';
?>
<!doctype html>
<html lang="bg" class="<?= e($classes) ?>" style="<?= e($style) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($fullTitle) ?></title>
<?php if (!empty($description)): ?>
<meta name="description" content="<?= e($description) ?>">
<?php endif ?>
<?php if (!Config::get('production')): ?>
<meta name="robots" content="noindex, nofollow">
<?php elseif (!empty($noindex)): ?>
<meta name="robots" content="noindex, follow">
<?php endif ?>
<?php if (!empty($canonical)): ?>
<link rel="canonical" href="<?= e($canonical) ?>">
<?php endif ?>
<?php /* Same address as in site.css (no ?v=), or the browser downloads the font twice. */ ?>
<link rel="preload" href="/assets/fonts/montserrat-cyrillic.woff2" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="<?= e(asset('site.css')) ?>">
<script src="<?= e(asset('site.js')) ?>" defer></script>
</head>
<body>
<a class="skip" href="#main">Към съдържанието</a>
<?= partial('header', ['nav' => $nav ?? null]) ?>
<main id="main">
<?= raw($content) ?>
</main>
<?= partial('footer') ?>
</body>
</html>
