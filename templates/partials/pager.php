<?php
/**
 * Numbered page links: ← Назад 1 … 4 5 6 … 20 Напред →
 * $pager  ItTour\Pager
 * $url    fn (int $page): string
 */
if ($pager->pages < 2) {
    return;
}
?>
<nav class="pager" aria-label="Страници">
  <?php if ($pager->page > 1): ?>
  <a class="pill" href="<?= e($url($pager->page - 1)) ?>" rel="prev">← Назад</a>
  <?php endif ?>
  <?php foreach ($pager->links() as $n): ?>
  <?php if ($n === null): ?>
  <span class="gap" aria-hidden="true">…</span>
  <?php elseif ($n === $pager->page): ?>
  <a class="pill on" href="<?= e($url($n)) ?>" aria-current="page"><span class="visually-hidden">Страница </span><?= e($n) ?></a>
  <?php else: ?>
  <a class="pill" href="<?= e($url($n)) ?>"><span class="visually-hidden">Страница </span><?= e($n) ?></a>
  <?php endif ?>
  <?php endforeach ?>
  <?php if ($pager->page < $pager->pages): ?>
  <a class="pill" href="<?= e($url($pager->page + 1)) ?>" rel="next">Напред →</a>
  <?php endif ?>
</nav>
