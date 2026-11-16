<?php
/**
 * Temporary home page until the real ones exist.
 * $heading, $season
 */
?>
<section class="page-hero">
  <div class="wrap">
    <h1 lang="en"><?= e($heading) ?></h1>
    <p class="lead">Тази страница още се прави.</p>
  </div>
</section>
<section class="tight">
  <div class="wrap">
    <div class="other-stops not-found-stops">
      <?php foreach ($season as $ev): ?>
      <a class="stop" style="--c:<?= e(css_colour($ev['colour'])) ?>" href="<?= e(city_url(ItTour\Events::city($ev))) ?>">
        <span class="cdot" aria-hidden="true"></span>
        <div><h3 lang="en"><?= e($ev['name']) ?></h3><p><?= e(date_bg($ev['date'], 'short')) ?></p></div>
      </a>
      <?php endforeach ?>
    </div>
  </div>
</section>
