<?php
/** A speaker's profile (src/Pages/SpeakerPage.php), on any domain. */
use ItTour\Speakers;

$s = $speaker;
?>
<section class="page-hero">
  <div class="wrap">
    <nav class="crumbs" aria-label="Път"><a href="<?= e(main_url('/')) ?>">IT Tour</a><span aria-hidden="true">/</span><a href="<?= e(main_url('/speakers')) ?>">Лектори</a><span aria-hidden="true">/</span><span aria-current="page"><?= e($s['name']) ?></span></nav>
    <div class="profile">
      <?php if ($s['photo']): ?>
      <?= partial('img', ['media' => $s['photo'], 'width' => 220, 'sizes' => '(max-width: 960px) 160px, 220px', 'alt' => $s['name'], 'class' => 'face', 'eager' => true]) ?>
      <?php else: ?>
      <span class="face no-photo" aria-hidden="true"><?= e(mb_substr($s['name'], 0, 1)) ?></span>
      <?php endif ?>
      <div>
        <span class="eyebrow">Лектор на IT Tour · <?= e(plural_bg($talkCount, 'лекция', 'лекции')) ?></span>
        <h1><?= e($s['name']) ?></h1>
        <?php if ($s['role'] || $s['company']): ?><p class="role"><?= e(implode(', ', array_filter([$s['role'], $s['company']]))) ?></p><?php endif ?>
        <?php if ($s['bio']): ?><div class="lead bio"<?= raw(lang_attr($s['bio'])) ?>><?= raw(text_html($s['bio'])) ?></div><?php endif ?>
        <?php if ($s['links']): ?>
        <ul class="links">
          <?php foreach ($s['links'] as $url): ?>
          <li><a class="pill" href="<?= e($url) ?>" rel="me nofollow"><?= e(Speakers::linkLabel($url)) ?> ↗</a></li>
          <?php endforeach ?>
        </ul>
        <?php endif ?>
      </div>
    </div>
  </div>
</section>

<?php if ($upcoming): ?>
<section class="tight">
  <div class="wrap">
    <div class="sec-head"><div><span class="eyebrow">Предстои</span><h2><?= e(count($upcoming) === 1 ? 'Предстояща лекция' : 'Предстоящи лекции') ?></h2></div></div>
    <?php foreach ($upcoming as $t): $ev = $t['event']; ?>
    <div class="upcoming-talk" style="--c:<?= e(css_colour($ev['colour'])) ?>">
      <div class="meta"><span class="tag" lang="en"><?= e($ev['name']) ?></span><span><?= e(date_bg($ev['date'], 'short')) ?><?php if ($ev['venue_name']): ?> · <?= e($ev['venue_name']) ?><?php endif ?></span></div>
      <h3<?= raw(lang_attr($t['title'])) ?>><a href="<?= e(talk_url($t)) ?>"><?= e($t['title']) ?></a></h3>
      <?php if ($t['description']): ?><p><?= e(excerpt($t['description'])) ?></p><?php endif ?>
      <a class="more" href="<?= e(city_url(['domain' => $ev['city_domain']], '/archive/' . $ev['year'] . '#schedule')) ?>">Виж цялата програма →</a>
    </div>
    <?php endforeach ?>
  </div>
</section>
<?php endif ?>

<?php if ($past): ?>
<section class="tight talks-section">
  <div class="wrap">
    <div class="sec-head"><div><span class="eyebrow">Лекции</span><h2>Лекции на IT Tour</h2></div></div>
    <div class="talk-grid">
      <?php foreach ($past as $t): ?>
      <?= partial('talk-card', ['talk' => $t, 'showSpeakers' => count($t['speakers']) > 1]) ?>
      <?php endforeach ?>
    </div>
  </div>
</section>
<?php endif ?>
