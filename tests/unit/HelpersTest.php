<?php

use ItTour\Router;

return [
    'e() escapes HTML and quotes' => function () {
        same('&lt;script&gt;&quot;x&quot;&apos;y&apos;&amp;', e('<script>"x"\'y\'&'));
    },
    'Bulgarian dates' => function () {
        same('събота, 22 август 2026', date_bg('2026-08-22', 'long'));
        same('22 август 2026', date_bg('2026-08-22'));
        same('30 май', date_bg('2026-05-30', 'short'));
        same('01.09', date_bg('2026-09-01', 'num'));
    },
    'ink colour gives readable text on city colours' => function () {
        same('#1d1d1d', ink_for('#C8CA08'));   // Plovdiv yellow-green
        same('#1d1d1d', ink_for('#6dc4aa'));   // Varna mint
        same('#ffffff', ink_for('#375992'));   // navy
        same('#ffffff', ink_for('#000'));
    },
    'css_colour only lets colours through' => function () {
        same('#6dc4aa', css_colour('#6DC4AA'));
        same('#375992', css_colour('red;background:url(x)'));
        same('#375992', css_colour(null));
    },
    'router matches patterns and rules' => function () {
        $r = (new Router())
            ->add('/', fn () => 'home')
            ->add('/archive/{year:\d{4}}', fn () => 'edition')
            ->add('/talks/{id:\d+}-{slug}', fn () => 'talk');
        same('home', $r->match('/')[0]());
        same(['year' => '2015'], $r->match('/archive/2015')[1]);
        same(null, $r->match('/archive/15'));
        same(['id' => '312', 'slug' => 'kak-da'], $r->match('/talks/312-kak-da')[1]);
        same(null, $r->match('/talks/x-y'));
    },
    'pager links: first, neighbours, last, gaps' => function () {
        same([1, 2, null, 21], (new ItTour\Pager(491, 1))->links());
        same([1, 2, 3, 4, 5, null, 21], (new ItTour\Pager(491, 4))->links());
        same([1, null, 9, 10, 11, null, 21], (new ItTour\Pager(491, 10))->links());
        same([1, null, 20, 21], (new ItTour\Pager(491, 21))->links());
        same([1], (new ItTour\Pager(0, 1))->links());
        ok((new ItTour\Pager(0, 1))->exists(), 'page 1 of an empty list');
        ok(!(new ItTour\Pager(491, 22))->exists(), 'page past the end');
    },
    'page numbers from the address' => function () {
        same(1, ItTour\Pager::number(ItTour\Request::get('x', '/talks')));
        same(3, ItTour\Pager::number(ItTour\Request::get('x', '/talks?page=3')));
        foreach (['0', '-1', 'abc', '2.5', '99999999'] as $bad) {
            same(null, ItTour\Pager::number(ItTour\Request::get('x', "/talks?page=$bad")), $bad);
        }
    },
];
