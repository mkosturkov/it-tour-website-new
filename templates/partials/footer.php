<?php
/** Site footer: the cities, the main pages, contact and social links. */
use ItTour\Cities;
use ItTour\Settings;

$email = Settings::get('contact_email');
?>
<footer>
  <div class="wrap foot">
    <div>
      <a class="logo" href="<?= e(main_url('/')) ?>"><img class="mark" src="/assets/img/it-tour-dark.svg" alt="IT Tour, начало" width="50" height="58"></a>
      <p><?= e(Settings::get('tagline')) ?></p>
    </div>
    <div>
      <h2 class="h4">Конференции</h2>
      <ul>
        <?php foreach (Cities::active() as $c): ?>
        <li><a href="<?= e(city_url($c)) ?>"><span class="cdot" style="--c:<?= e(css_colour($c['colour'])) ?>"></span><span lang="en"><?= e($c['brand'] ?? $c['name']) ?></span></a></li>
        <?php endforeach ?>
      </ul>
    </div>
    <div>
      <h2 class="h4">IT Tour</h2>
      <ul>
        <li><a href="<?= e(main_url('/talks')) ?>">Лекции</a></li>
        <li><a href="<?= e(main_url('/speakers')) ?>">Лектори</a></li>
        <li><a href="<?= e(main_url('/archive')) ?>">Архив</a></li>
        <li><a href="<?= e(main_url('/team')) ?>">Екип</a></li>
      </ul>
    </div>
    <div>
      <h2 class="h4">Контакти</h2>
      <ul>
        <?php if ($email): ?>
        <li><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></li>
        <?php endif ?>
        <?php foreach (Settings::json('social_links') as $link): ?>
        <li><a href="<?= e($link['url']) ?>" rel="me"><?= e($link['name']) ?></a></li>
        <?php endforeach ?>
      </ul>
    </div>
  </div>
  <div class="cbar" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></div>
</footer>
