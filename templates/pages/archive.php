<?php
/**
 * Archive of all editions (src/Pages/Archive.php).
 * Filters are plain links and a GET form, so they work without JavaScript;
 * site.js submits the year form as soon as it changes.
 */
$filterUrl = function (?string $c, ?int $y): string {
    $q = array_filter(['city' => $c, 'year' => $y]);
    return main_url('/archive' . ($q ? '?' . http_build_query($q) : ''));
};
$latestYear = $years[0];
?>
<section class="page-hero">
  <div class="wrap">
    <nav class="crumbs" aria-label="Път"><a href="<?= e(main_url('/')) ?>">IT Tour</a><span aria-hidden="true">/</span><span aria-current="page">Архив</span></nav>
    <h1>Всички издания от <?= e($firstYear) ?> насам</h1>
    <p class="lead"><?= e($total) ?> конференции в <?= e($cityCount) ?> града. Всяко издание има своя страница с програмата, лекторите и видеата.</p>
  </div>
</section>

<section class="tight archive-list">
  <div class="wrap">
    <div class="filters">
      <div class="pills" role="group" aria-label="Град">
        <a class="pill<?php if (!$city): ?> on<?php endif ?>" href="<?= e($filterUrl(null, $year)) ?>"<?php if (!$city): ?> aria-current="true"<?php endif ?>>Всички градове</a>
        <?php foreach ($cities as $c): ?>
        <a class="pill<?php if ($city === $c['slug']): ?> on<?php endif ?>" href="<?= e($filterUrl($c['slug'], $year)) ?>"<?php if ($city === $c['slug']): ?> aria-current="true"<?php endif ?>><span class="cdot" style="--c:<?= e(css_colour($c['colour'])) ?>"></span><?= e($c['name']) ?></a>
        <?php endforeach ?>
      </div>
      <form class="year-form" method="get" action="<?= e(main_url('/archive')) ?>" data-autosubmit>
        <?php if ($city): ?><input type="hidden" name="city" value="<?= e($city) ?>"><?php endif ?>
        <label class="visually-hidden" for="archive-year">Година</label>
        <select id="archive-year" name="year">
          <option value="">Всички години</option>
          <?php foreach ($years as $y): ?>
          <option value="<?= e($y) ?>"<?php if ($y === $year): ?> selected<?php endif ?>><?= e($y) ?></option>
          <?php endforeach ?>
        </select>
        <button class="btn btn-ghost btn-sm" type="submit">Покажи</button>
      </form>
    </div>

    <?php if (!$byYear): ?>
    <p class="empty">Няма издания за този избор. <a href="<?= e($filterUrl(null, null)) ?>">Покажи всички</a>.</p>
    <?php endif ?>

    <?php foreach ($byYear as $y => $editions): ?>
    <div class="year-head">
      <h2><?= e($y) ?></h2>
      <span><?php if ($y === $latestYear && $finished[$y] < $perYear[$y]): ?>сезонът тече · <?= e($finished[$y]) ?> от <?= e($perYear[$y]) ?> издания минаха<?php else: ?><?= e(plural_bg($perYear[$y], 'издание', 'издания')) ?><?php endif ?></span>
    </div>
    <div class="ed-grid">
      <?php foreach ($editions as $ev): ?>
      <a class="ed" href="<?= e(city_url(ItTour\Events::city($ev), '/archive/' . $ev['year'])) ?>" style="--c:<?= e(css_colour($ev['colour'])) ?>">
        <?php if ($ev['logo']): ?>
        <?= partial('img', ['media' => $ev['logo'], 'width' => 56, 'sizes' => '56px', 'alt' => '']) ?>
        <?php else: ?>
        <span class="ed-dot" aria-hidden="true"></span>
        <?php endif ?>
        <div>
          <h3 lang="en"><?= e($ev['name']) ?></h3>
          <p><?= e(date_bg($ev['date'], 'num')) ?><?php if ($ev['venue_name']): ?> · <?= e($ev['venue_name']) ?><?php endif ?></p>
        </div>
        <div class="stats"><span><?= e(plural_bg((int) $ev['talk_count'], 'лекция', 'лекции')) ?></span><?php if ($ev['video_count']): ?><span><?= e(plural_bg((int) $ev['video_count'], 'видео', 'видеа')) ?></span><?php endif ?></div>
      </a>
      <?php endforeach ?>
    </div>
    <?php endforeach ?>
  </div>
</section>
