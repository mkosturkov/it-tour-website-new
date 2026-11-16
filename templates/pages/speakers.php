<?php
/**
 * Speaker directory (src/Pages/SpeakersDirectory.php). Same instant search as
 * the talks library ([data-live-search] in site.js).
 */
?>
<section class="page-hero">
  <div class="wrap">
    <nav class="crumbs" aria-label="Път"><a href="<?= e(main_url('/')) ?>">IT Tour</a><span aria-hidden="true">/</span><span aria-current="page">Лектори</span></nav>
    <h1>Лекторите на IT Tour</h1>
    <p class="lead"><?= e($all) ?> души са говорили на сцената. Всеки има профил с всичките си лекции.</p>
  </div>
</section>

<section class="tight list-page">
  <div class="wrap">
    <form class="filters" role="search" method="get" action="<?= e(main_url('/speakers')) ?>" data-live-search>
      <label class="visually-hidden" for="sp-q">Търси по име</label>
      <input type="search" id="sp-q" name="q" value="<?= e($filters['q']) ?>" placeholder="Търси по име" autocomplete="off" maxlength="100">
      <label class="visually-hidden" for="sp-city">Град</label>
      <select id="sp-city" name="city">
        <option value="">Всички градове</option>
        <?php foreach ($cities as $c): ?>
        <option value="<?= e($c['slug']) ?>"<?php if ($filters['city'] === $c['slug']): ?> selected<?php endif ?>><?= e($c['name']) ?></option>
        <?php endforeach ?>
      </select>
      <label class="visually-hidden" for="sp-year">Година</label>
      <select id="sp-year" name="year">
        <option value="">Всички години</option>
        <?php foreach ($years as $y): ?>
        <option value="<?= e($y) ?>"<?php if ($filters['year'] === $y): ?> selected<?php endif ?>><?= e($y) ?></option>
        <?php endforeach ?>
      </select>
      <button class="btn btn-primary btn-sm" type="submit">Търси</button>
    </form>
    <p class="visually-hidden" role="status" data-results-status></p>
    <div class="results" id="results" data-results>
      <?= partial('speaker-results', get_defined_vars()) ?>
    </div>
  </div>
</section>
