<?php
/**
 * The speaker directory's results: count, cards and page links. Rendered
 * inside pages/speakers.php, and alone for instant search (X-Partial: results).
 */
$url = fn (int $p): string => list_url('/speakers', $filters + ['page' => $p]);
?>
<?php if (!$speakers): ?>
<p class="empty">Няма лектори за това търсене. <a href="<?= e(main_url('/speakers')) ?>">Изчисти филтрите</a></p>
<?php else: ?>
<p class="count-line"><?= e(plural_bg($pager->total, 'лектор', 'лектори')) ?><?php if ($pager->pages > 1): ?> · страница <?= e($pager->page) ?> от <?= e($pager->pages) ?><?php endif ?></p>
<div class="speakers">
  <?php foreach ($speakers as $sp): ?>
  <a class="speaker" href="<?= e(main_url('/speakers/' . $sp['id'])) ?>" style="--c:<?= e(css_colour($sp['latest']['colour'])) ?>">
    <?php if ($sp['photo']): ?>
    <?= partial('img', ['media' => $sp['photo'], 'width' => 200, 'sizes' => '(max-width: 560px) 40vw, 200px', 'alt' => '', 'class' => 'face']) ?>
    <?php else: ?>
    <span class="face no-photo" aria-hidden="true"><?= e(mb_substr($sp['name'], 0, 1)) ?></span>
    <?php endif ?>
    <b><?= e($sp['name']) ?></b>
    <?php if ((int) $sp['talk_count'] === 1): ?>
    <span lang="en"><?= e($sp['latest']['name']) ?></span>
    <?php else: ?>
    <span><?= e(plural_bg((int) $sp['talk_count'], 'лекция', 'лекции')) ?> · <?= e(implode(', ', $sp['cities'])) ?></span>
    <?php endif ?>
  </a>
  <?php endforeach ?>
</div>
<?= partial('pager', ['pager' => $pager, 'url' => $url]) ?>
<?php endif ?>
