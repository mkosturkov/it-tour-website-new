<?php
/**
 * it-tour.bg/llms.txt: a plain Markdown guide for AI tools (llmstxt.org).
 * Not HTML: values are printed with raw(), and nothing here comes from visitors.
 */
use ItTour\Events;
use ItTour\Settings;

$md = fn (string $s): string => (str_replace(['[', ']'], ['(', ')'], $s));
?>
# IT Tour

> <?= raw($md(Settings::get('tagline'))) ?>


IT Tour is a series of free one-day conferences for programmers and everyone in IT, held every year since <?= raw((string) min($years)) ?> in Bulgarian cities, one city a month. Talks are mostly in Bulgarian; entry is free. Each city's conference has its own site (for example varnaconf.com); it-tour.bg covers the whole tour.

## Season <?= raw((string) $season['year']) ?>


<?php foreach ($season['events'] as $ev): ?>
- [<?= raw($md($ev['name'])) ?>](<?= raw(city_url(Events::city($ev))) ?>): <?= raw($md(date_bg($ev['date'], 'long'))) ?>, <?= raw($md(trim(($ev['venue_name'] ?? '') . ', ' . $ev['city_name'], ', '))) ?><?= raw($ev['state'] === 'finished' ? ' (held)' : '') ?>

<?php endforeach ?>

## Explore

- [Archive](<?= raw(main_url('/archive')) ?>): all <?= raw((string) $editions) ?> editions since <?= raw((string) min($years)) ?>, each with its programme, speakers and videos
- [Talks](<?= raw(main_url('/talks')) ?>): <?= raw((string) $counts['talks']) ?> talks, <?= raw((string) $counts['videos']) ?> with a recording; searchable by title or speaker
- [Speakers](<?= raw(main_url('/speakers')) ?>): everyone who has spoken, each with a profile and all their talks
- [Team](<?= raw(main_url('/team')) ?>): the volunteers behind IT Tour
