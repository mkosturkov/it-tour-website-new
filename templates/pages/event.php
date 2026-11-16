<?php
/**
 * An edition's page on its city site (see src/Pages/EventPage.php for the data).
 * Sections follow the event's state: upcoming (countdown), live (player, now on
 * stage), finished (talk and video counts). Empty sections are left out.
 */
use ItTour\Events;
use ItTour\Site;

$e = $event;
$cityHome = city_url(Events::city($e));
$hasParty = $e['party_visible'] && $e['party_name'];
$sections = array_filter([
    'live' => $state === 'live' && ($e['live_url'] || $e['stream_url']) ? 'На живо' : null,
    'schedule' => $slots ? 'Програма' : null,
    'speakers' => $speakers ? 'Лектори' : null,
    'venue' => $e['venue_name'] ? 'Място' : null,
    'after-party' => $hasParty ? 'After party' : null,
    'partners' => $sponsors ? 'Партньори' : null,
    'editions' => count($years) > 1 ? 'Минали издания' : null,
]);
$stateText = [
    'upcoming' => $slots ? 'Програмата е публикувана' : 'Предстои',
    'live' => 'На живо сега',
    'finished' => 'Събитието завърши',
][$state];
?>
<section class="ev-hero">
  <div class="wrap ev-grid">
    <div>
      <div class="ev-brand">
        <?= partial('img', ['media' => $logo, 'width' => 84, 'sizes' => '84px', 'alt' => 'Лого на ' . $e['name'], 'eager' => true]) ?>
        <span class="ev-word" aria-hidden="true" lang="en"><?= e($wordmark[0]) ?><br><?= e($wordmark[1]) ?></span>
        <a class="part-of" href="<?= e(main_url('/')) ?>"><span class="mark"></span>Част от IT Tour <?= e($e['year']) ?><?php if ($stop): ?> · спирка <?= e($stop) ?> от <?= e(count($season)) ?><?php endif ?></a>
      </div>
      <h1 lang="en"><?= e(preg_replace('/\s*\d{4}$/', '', $e['name'])) ?> <span><?= e($e['year']) ?></span></h1>
      <span class="state-chip<?php if ($state === 'live'): ?> live<?php endif ?>"><span class="dot"></span><?= e($stateText) ?></span>
      <div class="ev-facts">
        <div><b><?= e(mb_ucfirst(date_bg($e['date'], 'long'))) ?></b><?= e($e['starts_at']) ?> – <?= e($e['ends_at']) ?><?php if ($hasParty): ?>, после after party<?php endif ?></div>
        <?php if ($e['venue_name']): ?>
        <div><b><?= e($e['venue_name']) ?></b><?= e($e['venue_address']) ?></div>
        <?php endif ?>
        <div><b>Вход свободен</b>без билети и регистрация</div>
      </div>
      <div class="cta">
        <?php if ($state === 'live' && isset($sections['live'])): ?>
        <a class="btn btn-primary" href="#live">▶ Гледай на живо</a>
        <?php endif ?>
        <?php if ($slots): ?>
        <a class="btn <?= e($state === 'live' && isset($sections['live']) ? 'btn-ghost' : 'btn-primary') ?>" href="#schedule">Виж програмата</a>
        <?php endif ?>
        <?php if ($e['facebook_url']): ?>
        <a class="btn btn-ghost" href="<?= e($e['facebook_url']) ?>">Събитието във Facebook</a>
        <?php endif ?>
      </div>
    </div>

    <aside class="side-card" aria-label="<?= e($state === 'upcoming' ? 'Оставащо време' : 'Накратко') ?>">
      <?php if ($state === 'upcoming'):
          $left = $now->diff(Events::startsAt($e)); ?>
      <h2 class="h3">Остават</h2>
      <div class="countdown" data-countdown="<?= e(Events::startsAt($e)->format(DATE_ATOM)) ?>">
        <div><b data-unit="d"><?= e($left->days) ?></b><span>дни</span></div>
        <div><b data-unit="h"><?= e(sprintf('%02d', $left->h)) ?></b><span>часа</span></div>
        <div><b data-unit="m"><?= e(sprintf('%02d', $left->i)) ?></b><span>минути</span></div>
      </div>
      <p>Вратите отварят малко преди <?= e($doorsOpen) ?>. Входът е свободен, не е нужна регистрация.</p>
      <?php elseif ($state === 'live'): ?>
      <h2 class="h3">В момента</h2>
      <?php if ($onStage): ?>
      <p class="onstage-now"><b<?= raw(lang_attr($onStage['talk']['title'] ?? $onStage['title'])) ?>><?= e($onStage['talk']['title'] ?? $onStage['title']) ?></b><?php if (!empty($onStage['talk']['speakers'])): ?><br><?= e(implode(', ', array_column($onStage['talk']['speakers'], 'name'))) ?><?php endif ?></p>
      <?php else: ?>
      <p>Събитието тече.</p>
      <?php endif ?>
      <?php else: ?>
      <h2 class="h3"><?= e($e['name']) ?> в числа</h2>
      <ul class="ev-stats">
        <li><b><?= e($talkCount) ?></b> <?= e($talkCount === 1 ? 'лекция' : 'лекции') ?></li>
        <li><b><?= e(count($speakers)) ?></b> <?= e(count($speakers) === 1 ? 'лектор' : 'лектори') ?></li>
        <li><b><?= e($videoCount) ?></b> <?= e($videoCount === 1 ? 'видео' : 'видеа') ?></li>
      </ul>
      <?php endif ?>
    </aside>
  </div>
</section>

<?php if (count($sections) > 1): ?>
<nav class="ev-nav" aria-label="Секции на събитието"><div class="wrap"><ul>
  <?php foreach ($sections as $id => $label): ?>
  <li><a href="#<?= e($id) ?>"><?= e($label) ?></a></li>
  <?php endforeach ?>
</ul></div></nav>
<?php endif ?>

<?php if (isset($sections['live'])): ?>
<section id="live">
  <div class="wrap">
    <div class="sec-head"><div><span class="eyebrow">На живо</span><h2>Гледай от вкъщи</h2></div></div>
    <div class="player-wrap">
      <div class="player">
        <?php if ($e['live_url']): ?>
        <?= partial('embed', ['url' => $e['live_url'], 'title' => 'Излъчване на живо: ' . $e['name']]) ?>
        <?php endif ?>
        <?php if ($e['stream_url']): ?>
        <div class="bar">
          <div class="stream-links"><a class="btn btn-ghost btn-sm" href="<?= e($e['stream_url']) ?>">Адрес за външен плейър (VLC)</a></div>
        </div>
        <?php endif ?>
      </div>
      <?php if ($onStage): ?>
      <div class="onstage">
        <span class="label">На сцената</span>
        <h3<?= raw(lang_attr($onStage['talk']['title'] ?? $onStage['title'])) ?>><?= e($onStage['talk']['title'] ?? $onStage['title']) ?></h3>
        <?php if (!empty($onStage['talk']['speakers'])): ?><p><?= e(implode(', ', array_column($onStage['talk']['speakers'], 'name'))) ?></p><?php endif ?>
      </div>
      <?php endif ?>
    </div>
  </div>
</section>
<?php endif ?>

<?php if ($cfpOpen): ?>
<section class="alt" id="cfp">
  <div class="wrap">
    <div class="sec-head"><div><span class="eyebrow">Покана за лектори</span><h2>Искаш ли да говориш на <?= e($e['name']) ?>?</h2><p>Кандидатствай до <?= e(date_bg($e['cfs_deadline'])) ?>.</p></div></div>
  </div>
</section>
<?php endif ?>

<?php if ($slots): ?>
<section id="schedule">
  <div class="wrap">
    <div class="sec-head"><div><span class="eyebrow">Програма</span><h2><?= e(date_bg($e['date'], 'short')) ?></h2></div></div>
    <div class="schedule">
      <div class="schedule-head" aria-hidden="true"><span>Начало – край</span><span>Тема и лектор</span></div>
      <?php foreach ($slots as $slot):
          $t = $slot['talk'] ?? null;
          $classes = ['slot'];
          if (!$t) { $classes[] = 'break'; }
          if ($state === 'live' && $onStage && $slot['id'] === $onStage['id']) { $classes[] = 'now'; }
          elseif ($state === 'live' && $slot['ends_at'] && $slot['ends_at'] <= $now->format('H:i')) { $classes[] = 'past'; }
      ?>
      <div class="<?= e(implode(' ', $classes)) ?>">
        <span class="time"><?= e(time_range($slot['starts_at'], $slot['ends_at'])) ?></span>
        <div>
          <?php if ($t): ?>
          <h3<?= raw(lang_attr($t['title'])) ?>><a href="<?= e(talk_url($t)) ?>"><?= e($t['title']) ?></a></h3>
          <?php if ($t['description']): ?><p class="abstract"<?= raw(lang_attr($t['description'])) ?>><?= e(excerpt($t['description'])) ?></p><?php endif ?>
          <?php if ($t['speakers']): ?>
          <div class="who">
            <?php foreach ($t['speakers'] as $sp): ?>
            <a class="who-one" href="<?= e(speaker_url($sp)) ?>"><?= partial('img', ['media' => $sp['photo'], 'width' => 30, 'sizes' => '30px', 'alt' => '', 'class' => 'face']) ?><?= e($sp['name']) ?></a>
            <?php endforeach ?>
          </div>
          <?php endif ?>
          <?php else: ?>
          <h3><?= e($slot['title']) ?></h3>
          <?php endif ?>
          <?php if ($slot['parallel']): ?><span class="parallel">паралелно</span><?php endif ?>
        </div>
        <?php if ($t && $t['video_url']): ?>
        <a class="act" href="<?= e(talk_url($t)) ?>">▶ Видео</a>
        <?php endif ?>
      </div>
      <?php endforeach ?>
    </div>
  </div>
</section>
<?php endif ?>

<?php if ($speakers): ?>
<section class="alt" id="speakers">
  <div class="wrap">
    <div class="sec-head"><div><span class="eyebrow">Лектори</span><h2>Кой говори</h2></div><a class="more" href="<?= e(main_url('/speakers')) ?>">Всички лектори на IT Tour →</a></div>
    <div class="speakers">
      <?php foreach ($speakers as $sp): ?>
      <a class="speaker" href="<?= e(speaker_url($sp)) ?>">
        <?php if ($sp['photo']): ?>
        <?= partial('img', ['media' => $sp['photo'], 'width' => 200, 'sizes' => '(max-width: 560px) 40vw, 200px', 'alt' => '', 'class' => 'face']) ?>
        <?php else: ?>
        <span class="face no-photo" aria-hidden="true"><?= e(mb_substr($sp['name'], 0, 1)) ?></span>
        <?php endif ?>
        <b><?= e($sp['name']) ?></b>
        <span<?= raw(lang_attr($sp['talks'][0])) ?>><?= e(implode(' · ', $sp['talks'])) ?></span>
      </a>
      <?php endforeach ?>
    </div>
  </div>
</section>
<?php endif ?>

<?php if ($e['venue_name']): ?>
<section id="venue">
  <div class="wrap">
    <div class="sec-head"><div><span class="eyebrow">Място</span><h2>Как да стигнеш</h2></div></div>
    <?= partial('place', [
        'name' => $e['venue_name'], 'url' => $e['venue_url'], 'address' => $e['venue_address'],
        'directions' => $e['venue_directions'], 'map' => $e['venue_map_url'],
        'when' => date_bg($e['date'], 'long') . ', ' . $e['starts_at'] . ' – ' . $e['ends_at'],
    ]) ?>
  </div>
</section>
<?php endif ?>

<?php if ($hasParty): ?>
<section class="alt" id="after-party">
  <div class="wrap">
    <div class="sec-head"><div><span class="eyebrow">After party</span><h2>След лекциите</h2></div></div>
    <?= partial('place', [
        'name' => $e['party_name'], 'url' => $e['party_url'], 'address' => $e['party_address'],
        'directions' => $e['party_directions'], 'map' => $e['party_map_url'],
        'when' => $e['party_starts_at'] ? 'от ' . $e['party_starts_at'] : null,
    ]) ?>
  </div>
</section>
<?php endif ?>

<?php if ($sponsors): ?>
<section id="partners">
  <div class="wrap">
    <div class="sec-head"><div><span class="eyebrow">Партньори</span><h2>С подкрепата на</h2></div></div>
    <div class="sponsors">
      <?php foreach ($sponsors as $group): ?>
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
  </div>
</section>
<?php endif ?>

<?php if (count($season) > 1): ?>
<section class="alt tight" id="other-stops">
  <div class="wrap">
    <div class="sec-head"><div><span class="eyebrow">IT Tour <?= e($e['year']) ?></span><h2>Другите спирки</h2></div></div>
    <div class="other-stops">
      <?php foreach ($season as $other): if ($other['id'] === $e['id']) { continue; } ?>
      <a class="stop" style="--c:<?= e(css_colour($other['colour'])) ?>" href="<?= e(city_url(Events::city($other), '/archive/' . $other['year'])) ?>">
        <span class="cdot" aria-hidden="true"></span>
        <div><h3 lang="en"><?= e($other['name']) ?></h3><p><?= e(date_bg($other['date'], 'short')) ?></p></div>
      </a>
      <?php endforeach ?>
    </div>
  </div>
</section>
<?php endif ?>

<?php if (isset($sections['editions'])): ?>
<section class="tight" id="editions">
  <div class="wrap">
    <div class="sec-head"><div><span class="eyebrow">Архив</span><h2>Минали издания на <span lang="en"><?= e(preg_replace('/\s*\d{4}$/', '', $e['name'])) ?></span></h2></div></div>
    <div class="editions">
      <?php foreach ($years as $y): if ($y === (int) $e['year']) { continue; } ?>
      <a class="pill" href="<?= e(here_url('/archive/' . $y)) ?>"><?= e($y) ?></a>
      <?php endforeach ?>
      <a class="pill" href="<?= e(main_url('/archive')) ?>">Всички градове и години →</a>
    </div>
  </div>
</section>
<?php endif ?>
