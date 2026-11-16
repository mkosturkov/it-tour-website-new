<?php
/**
 * Season pop-up on main-site pages (ItTour\Season::popup()): the next stop, or
 * the event that is live now. It slides in after a second; closing it hides it
 * for the rest of the visit (site.js, by data-popup).
 * $popup  ['event', 'live', 'days', 'hasSchedule', 'onStage']
 */
use ItTour\Events;

$ev = $popup['event'];
$url = city_url(Events::city($ev));
$when = match (true) {
    $popup['days'] <= 0 => 'Днес',
    $popup['days'] === 1 => 'Утре',
    default => 'След ' . $popup['days'] . ' дни',
};
$talk = $popup['onStage']['talk'] ?? null;
?>
<aside class="status" style="--c:<?= e($popup['live'] ? 'var(--live)' : css_colour($ev['colour'])) ?>" aria-label="Статус на сезона" data-popup="<?= e($ev['id'] . '-' . ($popup['live'] ? 'live' : 'next')) ?>">
  <div class="top">
    <span class="kind"><span class="dot<?php if ($popup['live']): ?> live<?php endif ?>"></span><?= e($popup['live'] ? 'На живо сега' : 'Следваща спирка') ?></span>
    <button class="x" type="button" aria-label="Затвори">×</button>
  </div>
  <div class="main">
    <?php if ($ev['logo']): ?><?= partial('img', ['media' => $ev['logo'], 'width' => 54, 'sizes' => '54px', 'alt' => '']) ?><?php endif ?>
    <div>
      <h3 lang="en"><?= e($popup['live'] ? $ev['name'] : preg_replace('/\s*\d{4}$/', '', $ev['name']) . ' · ' . date_bg($ev['date'], 'num')) ?></h3>
      <p><?php if (!$popup['live']): ?><?= e($when) ?> · <?php endif ?><?= e($ev['venue_name'] ?: $ev['city_name']) ?></p>
    </div>
  </div>
  <?php if ($talk): ?>
  <div class="now">На сцената: <b<?= raw(lang_attr($talk['title'])) ?>><?= e($talk['title']) ?></b><?php if ($talk['speakers']): ?>, <?= e(implode(', ', array_column($talk['speakers'], 'name'))) ?><?php endif ?></div>
  <?php endif ?>
  <div class="actions">
    <?php if ($popup['live']): ?>
    <a class="btn btn-primary" href="<?= e($url . '#live') ?>">▶ Гледай на живо</a>
    <?php elseif ($popup['hasSchedule']): ?>
    <a class="btn btn-primary" href="<?= e($url . '#schedule') ?>">Програмата</a>
    <?php else: ?>
    <a class="btn btn-primary" href="<?= e($url) ?>">Към събитието</a>
    <?php endif ?>
  </div>
</aside>
