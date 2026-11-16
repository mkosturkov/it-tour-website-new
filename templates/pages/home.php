<?php
/**
 * it-tour.bg homepage (src/Pages/Home.php): hero with numbers and this
 * season's badges, the stops (map and list), recent videos, season sponsors
 * and the newsletter. Sections without content are left out.
 */
use ItTour\Events;
use ItTour\Map;

$events = $season['events'];
$year = $season['year'];
$next = $season['next'];
$state = $season['state'];
$n = count($events);
$cities = [1 => 'Един град', 2 => 'Два града', 3 => 'Три града', 4 => 'Четири града', 5 => 'Пет града', 6 => 'Шест града', 7 => 'Седем града'][$n] ?? "$n града";
$stopUrl = fn (array $ev, string $hash = ''): string => city_url(Events::city($ev), '/' . $hash);
$badge = [
    'finished' => ['Мина', ''],
    'live' => ['На живо', 'live'],
    'upcoming' => ['Предстои', ''],
];
?>
<section class="hero" id="about">
  <div class="hero-photo"><img src="/assets/img/audience.jpg" alt="Публика на IT Tour конференция" width="1800" height="1200" fetchpriority="high"><img class="hero-logo" src="/assets/img/it-tour.svg" alt="" width="130" height="150"></div>
  <div class="wrap hero-grid">
    <div>
      <p class="wordmark" aria-label="IT Tour <?= e($year) ?>" lang="en"><span>it</span><span>tour</span><span><?= e($year) ?></span></p>
      <h1>Една конференция. <em><?= e($cities) ?>.</em> Всяко лято.</h1>
      <p class="lead"><?= e($tagline) ?></p>
      <?php if ($state === 'off'): ?>
      <p class="off-note">Сезон <?= e($year) ?> завърши. Датите за <?= e($year + 1) ?> предстоят<?php if ($newsletter): ?>; абонирай се и ще ти пишем, щом ги обявим<?php endif ?>.</p>
      <?php endif ?>
      <div class="cta">
        <?php if ($state === 'live'): ?>
        <a class="btn btn-primary" href="<?= e($stopUrl($next, '#live')) ?>">▶ На живо: <span lang="en"><?= e($next['name']) ?></span></a>
        <a class="btn btn-ghost" href="#stops">Спирките за <?= e($year) ?></a>
        <?php elseif ($state === 'upcoming'): ?>
        <a class="btn btn-primary" href="#stops">Виж спирките за <?= e($year) ?></a>
        <a class="btn btn-ghost" href="<?= e(main_url('/talks')) ?>">Гледай лекциите</a>
        <?php else: ?>
        <?php if ($newsletter): ?><a class="btn btn-primary" href="<?= e($newsletter) ?>">Абонирай се</a><?php endif ?>
        <a class="btn <?= e($newsletter ? 'btn-ghost' : 'btn-primary') ?>" href="<?= e(main_url('/talks')) ?>">Гледай лекциите</a>
        <?php endif ?>
      </div>
    </div>
    <div class="facts">
      <div class="fact"><b><?= e($facts['editions']) ?></b><span>проведени конференции</span></div>
      <div class="fact"><b>от <?= e($facts['since']) ?></b><span>всяка година</span></div>
      <div class="fact"><b><?= e($facts['talks']) ?></b><span>лекции</span></div>
      <div class="fact"><b><?= e($facts['speakers']) ?></b><span>лектори</span></div>
    </div>
  </div>
  <?php if ($events): ?>
  <div class="wrap badges">
    <?php foreach ($events as $ev): ?>
    <a<?php if ($ev['state'] === 'finished'): ?> class="done"<?php endif ?> href="<?= e($stopUrl($ev)) ?>">
      <?php if ($ev['logo']): ?>
      <?= partial('img', ['media' => $ev['logo'], 'width' => 120, 'sizes' => '120px', 'alt' => $ev['name'], 'eager' => true]) ?>
      <?php else: ?>
      <span class="badge-dot" style="--c:<?= e(css_colour($ev['colour'])) ?>" lang="en"><?= e($ev['name']) ?></span>
      <?php endif ?>
      <span><?= e(date_bg($ev['date'], 'short')) ?></span>
    </a>
    <?php endforeach ?>
  </div>
  <?php endif ?>
</section>

<?php if ($events): ?>
<section class="alt" id="stops">
  <div class="wrap">
    <div class="sec-head">
      <div>
        <span class="eyebrow">Сезон <?= e($year) ?></span>
        <h2>Спирките</h2>
        <p><?php if ($state === 'live'): ?>Сега сме в <?= e($next['city_name']) ?>. Гледай на живо или ела в залата.<?php elseif ($state === 'upcoming'): ?><?= e($cities) ?>, един на месец. Следващата спирка е <?= e($next['city_name']) ?>.<?php else: ?>Сезон <?= e($year) ?> завърши. Лекциите са в архива.<?php endif ?></p>
      </div>
      <a class="more" href="<?= e(main_url('/archive')) ?>">Целият архив от <?= e($facts['since']) ?> →</a>
    </div>
    <div class="stops-grid">
      <?php if ($points): ?>
      <div class="map-card">
        <svg viewBox="0 0 <?= e(Map::WIDTH) ?> <?= e(Map::HEIGHT) ?>" role="img" aria-label="Карта на България с градовете от сезон <?= e($year) ?>, свързани по реда на датите">
          <path class="bg-shape" d="<?= e(Map::OUTLINE) ?>"/>
          <?php
          // Solid up to the next stop (or all of it off-season), dotted after it.
          $split = $season['nextIndex'] ?? count($points) - 1;
          $line = fn (array $pts): string => 'M' . implode(' L', array_map(fn ($p) => $p['x'] . ' ' . $p['y'], $pts));
          $done = array_slice($points, 0, $split + 1);
          $todo = array_slice($points, $split);
          ?>
          <?php if (count($done) > 1): ?><path class="edge" d="<?= e($line($done)) ?>"/><?php endif ?>
          <?php if (count($todo) > 1): ?><path class="edge todo" d="<?= e($line($todo)) ?>"/><?php endif ?>
          <?php foreach ($points as $i => $p): ?>
          <?php
          $ev = $p['event'];
          $isNext = $i === $season['nextIndex'];
          $right = $p['x'] < 700;   // labels of the eastern cities go to the left of the dot
          $tx = $right ? $p['x'] + 30 : $p['x'] - 30;
          ?>
          <g class="node<?php if ($ev['state'] === 'finished'): ?> done<?php elseif ($isNext): ?> next<?php endif ?>">
            <?php if ($isNext): ?><circle class="ring" cx="<?= e($p['x']) ?>" cy="<?= e($p['y']) ?>" r="34" stroke="<?= e(css_colour($ev['colour'])) ?>"/><?php endif ?>
            <circle class="core" cx="<?= e($p['x']) ?>" cy="<?= e($p['y']) ?>" r="<?= e($isNext ? 22 : 18) ?>" fill="<?= e(css_colour($ev['colour'])) ?>"/>
            <text x="<?= e($tx) ?>" y="<?= e($p['y'] + 10) ?>"<?php if (!$right): ?> text-anchor="end"<?php endif ?>><?= e($ev['city_name'] === 'Велико Търново' ? 'Търново' : $ev['city_name']) ?></text>
            <text class="d" x="<?= e($tx) ?>" y="<?= e($p['y'] + 38) ?>"<?php if (!$right): ?> text-anchor="end"<?php endif ?>><?= e(date_bg($ev['date'], 'num')) ?></text>
          </g>
          <?php endforeach ?>
        </svg>
      </div>
      <?php endif ?>
      <div class="stop-list">
        <?php foreach ($events as $i => $ev): ?>
        <?php
        $isNext = $i === $season['nextIndex'];
        [$label, $class] = $isNext && $ev['state'] === 'upcoming' ? ['Следваща', 'next'] : $badge[$ev['state']];
        ?>
        <a class="stop<?php if ($ev['state'] === 'finished'): ?> done<?php elseif ($isNext): ?> next<?php endif ?>" href="<?= e($stopUrl($ev)) ?>" style="--c:<?= e(css_colour($ev['colour'])) ?>">
          <?php if ($ev['logo']): ?><?= partial('img', ['media' => $ev['logo'], 'width' => 52, 'sizes' => '52px', 'alt' => '']) ?><?php else: ?><span class="cdot" aria-hidden="true"></span><?php endif ?>
          <div>
            <h3 lang="en"><?= e(preg_replace('/\s*\d{4}$/', '', $ev['name'])) ?></h3>
            <p><?= e($ev['venue_name'] ?: $ev['city_name']) ?><?php if ($ev['state'] === 'upcoming' && $ev['schedule_published']): ?> · програмата е публикувана<?php endif ?></p>
          </div>
          <div class="date"><?= e(date_bg($ev['date'], 'num')) ?><br><span class="badge<?php if ($class): ?> <?= e($class) ?><?php endif ?>"><?= e($label) ?></span></div>
        </a>
        <?php endforeach ?>
      </div>
    </div>
  </div>
</section>
<?php endif ?>

<?php if ($videos): ?>
<section id="videos">
  <div class="wrap">
    <div class="sec-head">
      <div><span class="eyebrow">Записи</span><h2>Гледай лекциите</h2></div>
      <a class="more" href="<?= e(main_url('/talks?video=1')) ?>">Всички записи →</a>
    </div>
    <div class="talk-grid home-videos">
      <?php foreach ($videos as $talk): ?>
      <?= partial('talk-card', ['talk' => $talk, 'showSpeakers' => true]) ?>
      <?php endforeach ?>
    </div>
  </div>
</section>
<?php endif ?>

<?php if ($sponsors): ?>
<section id="sponsors"<?php if ($videos): ?> class="alt"<?php endif ?>>
  <div class="wrap">
    <div class="sec-head"><div><span class="eyebrow">Партньори <?= e($year) ?></span><h2>С подкрепата на</h2></div></div>
    <?= partial('sponsors', ['groups' => $sponsors]) ?>
  </div>
</section>
<?php endif ?>

<?php if ($newsletter): ?>
<section id="subscribe">
  <div class="wrap">
    <div class="subscribe">
      <div><h2>Не пропускай спирка</h2><p>Едно писмо преди всяка конференция: дата, програма и линк към стрийма.</p></div>
      <div><a class="btn btn-primary" href="<?= e($newsletter) ?>">Абонирай се</a></div>
    </div>
  </div>
</section>
<?php endif ?>
