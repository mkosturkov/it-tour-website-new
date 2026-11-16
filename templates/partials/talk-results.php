<?php
/**
 * The talks library's results: count, rows and page links. Rendered inside
 * pages/talks.php, and alone for instant search (X-Partial: results).
 */
use ItTour\Events;

$url = fn (int $p): string => list_url('/talks', $filters + ['page' => $p]);
?>
<?php if (!$talks): ?>
<p class="empty">Няма лекции за това търсене. <a href="<?= e(main_url('/talks')) ?>">Изчисти филтрите</a></p>
<?php else: ?>
<p class="count-line"><?= e(plural_bg($pager->total, 'лекция', 'лекции')) ?><?php if ($pager->pages > 1): ?> · страница <?= e($pager->page) ?> от <?= e($pager->pages) ?><?php endif ?></p>
<div class="rowlist talk-rows">
  <?php foreach ($talks as $t): ?>
  <?php $ev = $t['event']; ?>
  <a href="<?= e(talk_url($t)) ?>">
    <span class="when"><span class="cdot" style="--c:<?= e(css_colour($ev['colour'])) ?>"></span><?= e($ev['year']) ?></span>
    <div>
      <h3<?= raw(lang_attr($t['title'])) ?>><?= e($t['title']) ?></h3>
      <p><?php if ($t['speakers']): ?><?= e(implode(', ', array_column($t['speakers'], 'name'))) ?> · <?php endif ?><span lang="en"><?= e($ev['name']) ?></span></p>
    </div>
    <?php if (Events::state($ev) === 'upcoming'): ?>
    <span class="end">предстои</span>
    <?php elseif ($t['video_url']): ?>
    <span class="end more">▶ запис</span>
    <?php else: ?>
    <span class="end"></span>
    <?php endif ?>
  </a>
  <?php endforeach ?>
</div>
<?= partial('pager', ['pager' => $pager, 'url' => $url]) ?>
<?php endif ?>
