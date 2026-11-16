<?php
/**
 * Team page (src/Pages/TeamPage.php): one flat list. Until editors add
 * people it says the team will be introduced here.
 */
?>
<section class="page-hero">
  <div class="wrap">
    <nav class="crumbs" aria-label="Път"><a href="<?= e(main_url('/')) ?>">IT Tour</a><span aria-hidden="true">/</span><span aria-current="page">Екип</span></nav>
    <h1>Хората зад IT Tour</h1>
    <p class="lead">Всички в екипа са доброволци. Организират конференциите от залата до кафето, всяка година от 2012 насам.</p>
  </div>
</section>

<section class="tight team-page">
  <div class="wrap">
    <?php if (!$people): ?>
    <p class="empty">Скоро тук ще представим екипа.</p>
    <?php else: ?>
    <ul class="team-flat">
      <?php foreach ($people as $p): ?>
      <li class="member">
        <?php if ($p['photo']): ?>
        <?= partial('img', ['media' => $p['photo'], 'width' => 52, 'sizes' => '52px', 'alt' => '', 'class' => 'face']) ?>
        <?php else: ?>
        <span class="face no-photo" aria-hidden="true"><?= e(mb_substr($p['name'], 0, 1)) ?></span>
        <?php endif ?>
        <div>
          <b><?= e($p['name']) ?></b>
          <?php if ($p['role']): ?><span><?= e($p['role']) ?></span><?php endif ?>
          <?php if ($p['about']): ?><div class="about"><?= raw(text_html($p['about'])) ?></div><?php endif ?>
        </div>
      </li>
      <?php endforeach ?>
    </ul>
    <?php endif ?>
  </div>
</section>
