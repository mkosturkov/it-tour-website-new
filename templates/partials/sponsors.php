<?php
/**
 * Sponsors grouped by sponsorship type, logos linking to the sponsors.
 * $groups  from ItTour\Sponsors::forEvent() or ::forSeason()
 */
?>
<div class="sponsors">
  <?php foreach ($groups as $group): ?>
  <div class="tier">
    <span class="t"><?= e($group['type'] ?: 'Партньори') ?></span>
    <div class="logos">
      <?php foreach ($group['sponsors'] as $s): ?>
      <?php if ($s['url']): ?><a class="slogo" href="<?= e($s['url']) ?>"><?php else: ?><span class="slogo"><?php endif ?>
        <?php if ($s['logo']): ?><?= partial('img', ['media' => $s['logo'], 'width' => 160, 'sizes' => '160px', 'alt' => $s['name']]) ?><?php else: ?><?= e($s['name']) ?><?php endif ?>
      <?php if ($s['url']): ?></a><?php else: ?></span><?php endif ?>
      <?php endforeach ?>
    </div>
  </div>
  <?php endforeach ?>
</div>
