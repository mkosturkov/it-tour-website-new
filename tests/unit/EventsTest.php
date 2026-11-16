<?php

use ItTour\Clock;
use ItTour\Events;
use ItTour\Programme;

$event = ['date' => '2026-08-22', 'starts_at' => '10:30', 'ends_at' => '18:00', 'name' => 'VarnaConf 2026'];
$at = fn (string $when) => new DateTimeImmutable($when, Clock::zone());

return [
    'upcoming before the start, live during, finished after' => function () use ($event, $at) {
        same('upcoming', Events::state($event, $at('2026-08-01 12:00')));
        same('upcoming', Events::state($event, $at('2026-08-22 10:29')));
        same('live', Events::state($event, $at('2026-08-22 10:30')));
        same('live', Events::state($event, $at('2026-08-22 17:59')));
        same('finished', Events::state($event, $at('2026-08-22 18:00')));
        same('finished', Events::state($event, $at('2026-08-22 21:00')));   // the after party doesn't count
    },
    'state uses Bulgarian time, whatever the server zone' => function () use ($event) {
        // 07:45 UTC is 10:45 in Sofia (summer time): live.
        same('live', Events::state($event, new DateTimeImmutable('2026-08-22 07:45', new DateTimeZone('UTC'))));
    },
    'wordmark splits the name' => function () {
        same(['varna', 'conf 2026'], Events::wordmark(['name' => 'VarnaConf 2026']));
        same(['blagoevgrad', 'conf 2014'], Events::wordmark(['name' => 'BlagoevgradConf 2014']));
    },
    'now on stage is the latest started slot that has not ended' => function () {
        $slots = [
            ['id' => 1, 'starts_at' => '10:30', 'ends_at' => '11:00'],
            ['id' => 2, 'starts_at' => '11:00', 'ends_at' => '11:40'],
            ['id' => 3, 'starts_at' => '17:30', 'ends_at' => null],
        ];
        same(null, Programme::onStage($slots, '10:00'));
        same(1, Programme::onStage($slots, '10:59')['id']);
        same(2, Programme::onStage($slots, '11:00')['id']);
        same(null, Programme::onStage($slots, '12:00'));   // a gap
        same(3, Programme::onStage($slots, '19:00')['id']);
    },
    'excerpt cuts at a word' => function () {
        same('едно две…', excerpt('едно две три', 9));
        same('кратко', excerpt('кратко'));
    },
    'English titles are marked' => function () {
        same(' lang="en"', lang_attr('Microservices done right'));
        same('', lang_attr('AI на Правец 8'));
        same('', lang_attr('2026'));
    },
];
