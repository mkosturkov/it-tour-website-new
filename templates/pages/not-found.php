<?php
/**
 * 404 page: a short friendly message and links onwards. No search box.
 * $season: this season's events
 */
?>
<section class="page-hero">
  <div class="wrap">
    <span class="eyebrow">404</span>
    <h1>Тази страница я няма</h1>
    <p class="lead">Може адресът да е стар или да има грешка в него. Ето откъде можете да продължите.</p>
  </div>
</section>
<section class="tight">
  <div class="wrap">
    <?php if ($season): ?>
    <h2 class="h3">Събития през <?= e($season[0]['year']) ?></h2>
    <div class="other-stops not-found-stops">
      <?php foreach ($season as $ev): ?>
      <a class="stop" style="--c:<?= e(css_colour($ev['colour'])) ?>" href="<?= e(city_url(ItTour\Events::city($ev))) ?>">
        <span class="cdot" aria-hidden="true"></span>
        <div><h3 lang="en"><?= e($ev['name']) ?></h3><p><?= e(date_bg($ev['date'], 'short')) ?></p></div>
      </a>
      <?php endforeach ?>
    </div>
    <?php endif ?>
    <ul class="not-found-links">
      <li><a class="btn btn-ghost" href="<?= e(main_url('/archive')) ?>">Архив на всички издания</a></li>
      <li><a class="btn btn-ghost" href="<?= e(main_url('/talks')) ?>">Всички лекции</a></li>
      <li><a class="btn btn-ghost" href="<?= e(main_url('/speakers')) ?>">Всички лектори</a></li>
      <li><a class="btn btn-primary" href="<?= e(main_url('/')) ?>">Към началната страница</a></li>
    </ul>
  </div>
</section>
