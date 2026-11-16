<?php

use ItTour\Text;

return [
    'slug transliterates Bulgarian by the official rules' => function () {
        same('kak-da-testvame-legacy-kod', Text::slug('Как да тестваме legacy код?'));
        same('zhivot-tsar-chudo-shapka-shtastie-ayaks-yuli-yabalka', Text::slug('Живот цар чудо шапка щастие аякс юли ябълка'));
    },
    'slug writes "ия" at the end of a word as "ia"' => function () {
        same('sofia-istoria', Text::slug('София история'));
        same('bulgaria-istoriyata', Text::slug('България историята'));
    },
    'slug drops punctuation and accents' => function () {
        same('the-4c-s-of-cloud-native-security', Text::slug("The 4C's of Cloud Native Security"));
        same('cafe-c-net', Text::slug('Café — C#/.NET'));
    },
    'slug is cut at a whole word' => function () {
        $slug = Text::slug(str_repeat('дълга дума ', 20));
        ok(strlen($slug) <= 60, 'too long: ' . strlen($slug));
        ok(!str_ends_with($slug, '-'), 'ends with a hyphen');
        same('dalga-duma', substr($slug, 0, 10));
    },
    'slug of empty text is never empty' => function () {
        same('n', Text::slug('?!'));
    },
    'search text holds the Cyrillic and the Latin form' => function () {
        same('светлин наков svetlin nakov', Text::searchText('Светлин  Наков'));
        same('react за php react za php', Text::searchText('React', 'за PHP'));
        ok(str_contains(Text::searchText('Наков'), 'nakov'), 'Latin form missing');
        same('graphql', Text::searchText('GraphQL'));
    },
];
