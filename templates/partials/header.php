<?php
/**
 * Site header with the main menu. The menu is fixed and the same on every
 * domain; links go to the main site. Hidden in version 1: За IT Tour, Новини
 * and Включи се (next version). "Абонирай се" shows only when the newsletter
 * is on.
 *
 * $nav: key of the active item, or null
 */
use ItTour\Settings;

$items = [
    ['stops', '/#stops', 'Спирки'],
    ['talks', '/talks', 'Лекции'],
    ['speakers', '/speakers', 'Лектори'],
    ['archive', '/archive', 'Архив'],
    ['team', '/team', 'Екип'],
];
$newsletter = Settings::get('newsletter_url');
?>
<div class="cbar" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></div>
<header>
  <div class="wrap nav">
    <a class="logo" href="<?= e(main_url('/')) ?>"><img class="mark" src="/assets/img/it-tour-dark.svg" alt="IT Tour, начало" width="38" height="44"></a>
    <nav id="main-nav" aria-label="Основно меню">
      <ul>
        <?php foreach ($items as [$key, $path, $label]): ?>
        <li><a href="<?= e(main_url($path)) ?>"<?php if ($key === $nav): ?> aria-current="page"<?php endif ?>><?= e($label) ?></a></li>
        <?php endforeach ?>
      </ul>
    </nav>
    <div class="nav-right">
      <?php if ($newsletter): ?>
      <a class="btn btn-primary btn-sm" href="<?= e($newsletter) ?>">Абонирай се</a>
      <?php endif ?>
      <button class="menu-btn" type="button" aria-controls="main-nav" aria-expanded="false" aria-label="Меню"><span></span></button>
    </div>
  </div>
</header>
