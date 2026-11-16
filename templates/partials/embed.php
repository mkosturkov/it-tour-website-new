<?php
/**
 * A YouTube video or Google map that loads only when the visitor asks, so the
 * page stays light and nothing reaches Google before a click.
 *
 * Videos get two buttons (agreed 2026-11-16):
 *  - "Гледай тук" loads the YouTube player in the page WITHOUT autoplay, so the
 *    visitor presses YouTube's own play button and the play counts as a view
 *    (autoplayed embeds don't count on YouTube);
 *  - "Гледай в YouTube" opens the video on YouTube (or the app on phones).
 * Maps get one button that loads the map, and work as a link without JavaScript.
 *
 * $url    YouTube watch/live address, or a Google Maps embed address
 * $start  optional: seconds into the video where it should start (a talk in a full-day recording)
 * $still  optional media row: our own copy of the video's still image
 * $title  iframe title (for screen readers)
 * $label  button text for maps
 * $kind   'video' (default) or 'map'
 */
$kind ??= 'video';
$src = $url;
$open = $url;
if ($kind === 'video' && preg_match('#(?:v=|youtu\.be/|/live/|/embed/)([\w-]{11})#', $url, $m)) {
    $src = 'https://www.youtube-nocookie.com/embed/' . $m[1] . (!empty($start) ? '?start=' . (int) $start : '');
    $open = 'https://www.youtube.com/watch?v=' . $m[1] . (!empty($start) ? '&t=' . (int) $start . 's' : '');
} elseif ($kind === 'map') {
    $open = str_replace('output=embed', '', $url);
}
?>
<div class="embed embed-<?= e($kind) ?>" data-embed="<?= e($src) ?>" data-title="<?= e($title) ?>">
  <?php if (!empty($still)): ?><?= partial('img', ['media' => $still, 'width' => 800, 'sizes' => '(max-width: 960px) 100vw, 800px', 'alt' => '']) ?><?php endif ?>
  <?php if ($kind === 'video'): ?>
  <div class="embed-actions">
    <a class="embed-play" href="<?= e($open) ?>"><span class="play" aria-hidden="true">▶</span><span>Гледай тук</span></a>
    <a class="embed-out" href="<?= e($open) ?>">Гледай в YouTube ↗</a>
  </div>
  <?php else: ?>
  <a class="embed-play" href="<?= e($open) ?>"><span class="play" aria-hidden="true">▶</span><span><?= e($label) ?></span></a>
  <?php endif ?>
</div>
