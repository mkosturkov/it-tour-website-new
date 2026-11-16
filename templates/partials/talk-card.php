<?php
/**
 * A talk as a card: video still (or the edition's colour), edition, title, speakers.
 * $talk  from ItTour\Talks (with 'event', 'speakers', 'still')
 * $showSpeakers  list the speakers (false on a speaker's own profile when alone)
 */
$ev = $talk['event'];
$others = $talk['speakers'];
?>
<a class="card video talk-card<?php if (!$talk['still']): ?> no-still<?php endif ?>" href="<?= e(talk_url($talk)) ?>" style="--c:<?= e(css_colour($ev['colour'])) ?>">
  <?php if ($talk['still']): ?>
  <div class="still">
    <?= partial('img', ['media' => $talk['still'], 'width' => 400, 'sizes' => '(max-width: 560px) 100vw, (max-width: 960px) 50vw, 380px', 'alt' => '']) ?>
    <span class="play" aria-hidden="true">▶</span>
  </div>
  <?php endif ?>
  <div class="body">
    <span class="meta"><span class="tag" lang="en"><?= e($ev['name']) ?></span><?php if ($talk['video_url']): ?><span>видео</span><?php endif ?></span>
    <h3<?= raw(lang_attr($talk['title'])) ?>><?= e($talk['title']) ?></h3>
    <?php if (!empty($showSpeakers) && $others): ?><p><?= e(implode(', ', array_column($others, 'name'))) ?></p><?php endif ?>
  </div>
</a>
