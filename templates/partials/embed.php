<?php
/**
 * A YouTube video or Google map that loads only when clicked, so the page
 * stays light and nothing is sent to Google until the visitor asks.
 * Without JavaScript the button is a plain link to the video or map.
 *
 * $url    YouTube watch/live address, or a Google Maps embed address
 * $still  optional media row: our own copy of the video's still image
 *         (never loaded from YouTube, so nothing reaches Google before a click)
 * $title  iframe title (for screen readers)
 * $label  button text
 * $kind   'video' (default) or 'map'
 */
$kind ??= 'video';
$src = $url;
$open = $url;
if ($kind === 'video' && preg_match('#(?:v=|youtu\.be/|/live/|/embed/)([\w-]{11})#', $url, $m)) {
    $src = 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?autoplay=1';
    $open = 'https://www.youtube.com/watch?v=' . $m[1];
} elseif ($kind === 'map') {
    $open = str_replace('output=embed', '', $url);
}
?>
<div class="embed embed-<?= e($kind) ?>" data-embed="<?= e($src) ?>" data-title="<?= e($title) ?>">
  <?php if (!empty($still)): ?><?= partial('img', ['media' => $still, 'width' => 800, 'sizes' => '(max-width: 960px) 100vw, 800px', 'alt' => '']) ?><?php endif ?>
  <a class="embed-play" href="<?= e($open) ?>"><span class="play" aria-hidden="true">▶</span><span><?= e($label) ?></span></a>
</div>
