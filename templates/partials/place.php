<?php
/**
 * A venue or the after party: details on the left, map on the right.
 * $name, $url, $address, $directions, $map (Google Maps embed address), $when
 */
?>
<div class="venue">
  <div>
  <dl>
    <div><dt>Място</dt><dd><?php if ($url): ?><a href="<?= e($url) ?>"><?= e($name) ?></a><?php else: ?><?= e($name) ?><?php endif ?><?php if ($address): ?><br><?= e($address) ?><?php endif ?></dd></div>
    <?php if (!empty($when)): ?><div><dt>Кога</dt><dd><?= e(mb_ucfirst($when)) ?></dd></div><?php endif ?>
    <?php if ($directions): ?><div><dt>Как да стигнеш</dt><dd class="prose-sm"><?= raw(text_html($directions)) ?></dd></div><?php endif ?>
  </dl>
  <p class="place-open"><a class="btn btn-ghost btn-sm" href="<?= e(maps_link($name, $address)) ?>">Отвори в Google Maps ↗</a></p>
  </div>
  <?php if ($map): ?>
  <?= partial('embed', ['url' => $map, 'title' => 'Карта: ' . $name, 'label' => 'Покажи картата', 'kind' => 'map']) ?>
  <?php endif ?>
</div>
