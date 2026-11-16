<?php
/** One talk (src/Pages/TalkPage.php). */
$t = $talk;
$isYoutube = $t['video_url'] && preg_match('#youtube\.com|youtu\.be#', $t['video_url']);
?>
<section class="page-hero" style="--glow:<?= e(css_colour($event['colour'])) ?>">
  <div class="wrap">
    <nav class="crumbs" aria-label="Път"><a href="<?= e(main_url('/')) ?>">IT Tour</a><span aria-hidden="true">/</span><a href="<?= e(main_url('/talks')) ?>">Лекции</a><span aria-hidden="true">/</span><a href="<?= e($eventUrl) ?>" lang="en"><?= e($event['name']) ?></a></nav>
    <h1<?= raw(lang_attr($t['title'])) ?>><?= e($t['title']) ?></h1>
    <div class="meta talk-meta" style="--c:<?= e(css_colour($event['colour'])) ?>">
      <a class="tag" href="<?= e($eventUrl) ?>" lang="en"><?= e($event['name']) ?></a>
      <span><?= e(date_bg($event['date'])) ?> · <?= e($event['city_name']) ?></span>
    </div>
    <?php if ($t['speakers']): ?>
    <p class="talk-by">
      <?php foreach ($t['speakers'] as $i => $sp): ?><?php if ($i): ?>, <?php endif ?><a href="<?= e(main_url('/speakers/' . $sp['id'])) ?>"><?= e($sp['name']) ?></a><?php endforeach ?>
    </p>
    <?php endif ?>
  </div>
</section>

<section class="tight talk-body">
  <div class="wrap two-col">
    <div class="talk-main">
      <?php if ($isYoutube): ?>
      <?= partial('embed', ['url' => $t['video_url'], 'still' => $t['still'], 'title' => 'Видео: ' . $t['title'], 'label' => 'Гледай лекцията']) ?>
      <?php elseif ($upcoming): ?>
      <p class="talk-note">Лекцията предстои на <?= e(date_bg($event['date'])) ?>. Видеото ще се появи тук след събитието.</p>
      <?php endif ?>
      <?php if ($t['description']): ?>
      <div class="prose"<?= raw(lang_attr($t['description'])) ?>>
        <h2>За лекцията</h2>
        <?= raw(text_html($t['description'])) ?>
      </div>
      <?php endif ?>
      <?php if ($t['video_url'] || $t['slides_url']): ?>
      <div class="links">
        <?php if ($t['video_url']): ?><a class="btn btn-ghost btn-sm" href="<?= e($t['video_url']) ?>"><?= e($isYoutube ? 'Гледай в YouTube ↗' : 'Видео ↗') ?></a><?php endif ?>
        <?php if ($t['slides_url']): ?><a class="btn btn-ghost btn-sm" href="<?= e(str_starts_with($t['slides_url'], '/') ? main_url($t['slides_url']) : $t['slides_url']) ?>">Слайдове ↗</a><?php endif ?>
      </div>
      <?php endif ?>
    </div>
    <aside class="aside-card">
      <?php if ($t['speakers']): ?>
      <h2 class="eyebrow"><?= e(count($t['speakers']) === 1 ? 'Лектор' : 'Лектори') ?></h2>
      <?php foreach ($t['speakers'] as $sp): ?>
      <a class="aside-speaker" href="<?= e(main_url('/speakers/' . $sp['id'])) ?>">
        <?php if ($sp['photo']): ?><?= partial('img', ['media' => $sp['photo'], 'width' => 56, 'sizes' => '56px', 'alt' => '', 'class' => 'face']) ?><?php else: ?><span class="face no-photo" aria-hidden="true"><?= e(mb_substr($sp['name'], 0, 1)) ?></span><?php endif ?>
        <span><b><?= e($sp['name']) ?></b><br><span class="sub">Всички лекции →</span></span>
      </a>
      <?php endforeach ?>
      <hr>
      <?php endif ?>
      <?php if ($others): ?>
      <h2 class="eyebrow">Още от <span lang="en"><?= e($event['name']) ?></span></h2>
      <ul class="aside-list">
        <?php foreach ($others as $o): ?>
        <li><a href="<?= e(talk_url($o)) ?>"<?= raw(lang_attr($o['title'])) ?>><?= e($o['title']) ?></a></li>
        <?php endforeach ?>
      </ul>
      <?php endif ?>
      <a class="btn btn-ghost btn-sm" href="<?= e($eventUrl) ?>">Към изданието</a>
    </aside>
  </div>
</section>
