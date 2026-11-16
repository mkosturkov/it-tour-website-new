<?php
/**
 * A YouTube video or Google map that loads only when clicked, so the page
 * stays light and nothing is sent to Google until the visitor asks.
 * Without JavaScript the button is a plain link to the video or map.
 *
 * $url    YouTube watch/live address, or a Google Maps embed address
 * $title  iframe title (for screen readers)
 * $label  button text
 * $kind   'video' (default) or 'map'
 */
$kind ??= 'video';
$src = $url;
$open = $url;
$thumb = null;
if ($kind === 'video' && preg_match('#(?:v=|youtu\.be/|/live/|/embed/)([\w-]{11})#', $url, $m)) {
    $src = 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?autoplay=1';
    $open = 'https://www.youtube.com/watch?v=' . $m[1];
    $thumb = 'https://i.ytimg.com/vi/' . $m[1] . '/hqdefault.jpg';
} elseif ($kind === 'map') {
    $open = str_replace('output=embed', '', $url);
}
?>
<div class="embed embed-<?= e($kind) ?>" data-embed="<?= e($src) ?>" data-title="<?= e($title) ?>">
  <?php if ($thumb): ?><img src="<?= e($thumb) ?>" alt="" loading="lazy" width="480" height="360"><?php endif ?>
  <a class="embed-play" href="<?= e($open) ?>"><span class="play" aria-hidden="true">▶</span><span><?= e($label) ?></span></a>
</div>
