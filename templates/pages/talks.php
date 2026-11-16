<?php
/**
 * Talks library (src/Pages/TalksLibrary.php). The filter form is a plain GET
 * form; site.js turns it into instant search ([data-live-search]): it fetches
 * the results block from the same address and keeps the address in step.
 */
?>
<section class="page-hero">
  <div class="wrap">
    <nav class="crumbs" aria-label="Път"><a href="<?= e(main_url('/')) ?>">IT Tour</a><span aria-hidden="true">/</span><span aria-current="page">Лекции</span></nav>
    <h1>Библиотека с лекции</h1>
    <p class="lead"><?= e($counts['talks']) ?> лекции от всички градове и години, <?= e($counts['videos']) ?> от тях със запис. Търси по заглавие или лектор и филтрирай по град и година.</p>
  </div>
</section>

<section class="tight list-page">
  <div class="wrap">
    <form class="filters" role="search" method="get" action="<?= e(main_url('/talks')) ?>" data-live-search>
      <label class="visually-hidden" for="talk-q">Търси лекция или лектор</label>
      <input type="search" id="talk-q" name="q" value="<?= e($filters['q']) ?>" placeholder="Търси лекция или лектор" autocomplete="off" maxlength="100">
      <label class="visually-hidden" for="talk-city">Град</label>
      <select id="talk-city" name="city">
        <option value="">Всички градове</option>
        <?php foreach ($cities as $c): ?>
        <option value="<?= e($c['slug']) ?>"<?php if ($filters['city'] === $c['slug']): ?> selected<?php endif ?>><?= e($c['name']) ?></option>
        <?php endforeach ?>
      </select>
      <label class="visually-hidden" for="talk-year">Година</label>
      <select id="talk-year" name="year">
        <option value="">Всички години</option>
        <?php foreach ($years as $y): ?>
        <option value="<?= e($y) ?>"<?php if ($filters['year'] === $y): ?> selected<?php endif ?>><?= e($y) ?></option>
        <?php endforeach ?>
      </select>
      <label class="check"><input type="checkbox" name="video" value="1"<?php if ($filters['video']): ?> checked<?php endif ?>> Само със запис</label>
      <button class="btn btn-primary btn-sm" type="submit">Търси</button>
    </form>
    <p class="visually-hidden" role="status" data-results-status></p>
    <div class="results" id="results" data-results>
      <?= partial('talk-results', get_defined_vars()) ?>
    </div>
  </div>
</section>
