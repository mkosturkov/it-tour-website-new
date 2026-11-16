<?php

declare(strict_types=1);

namespace ItTour;

/** Transliteration, slugs and search text. */
final class Text
{
    /** Official Bulgarian transliteration (Закон за транслитерацията, 2009). */
    private const BG = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ж' => 'zh',
        'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f',
        'х' => 'h', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sht', 'ъ' => 'a', 'ь' => 'y',
        'ю' => 'yu', 'я' => 'ya',
        // Letters from other Cyrillic alphabets that turn up in names.
        'ё' => 'yo', 'ы' => 'y', 'э' => 'e', 'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
    ];

    public static function transliterate(string $s): string
    {
        $s = mb_strtolower($s, 'UTF-8');
        // The law's one named exception: България → Bulgaria.
        $s = preg_replace('/\bбългария\b/u', 'bulgaria', $s);
        // "ия" at the end of a word is "ia" (София → Sofia).
        $s = preg_replace('/ия\b/u', 'ia', $s);
        return strtr($s, self::BG);
    }

    /** "Как да тестваме legacy код?" → "kak-da-testvame-legacy-kod" */
    public static function slug(string $s, int $max = 60): string
    {
        $s = self::transliterate($s);
        // Remove accents from Latin letters (é → e).
        if (class_exists(\Transliterator::class)) {
            $s = \Transliterator::create('Any-Latin; Latin-ASCII')?->transliterate($s) ?? $s;
        }
        $s = strtolower($s);
        $s = trim(preg_replace('/[^a-z0-9]+/', '-', $s), '-');
        if (strlen($s) > $max) {
            $cut = substr($s, 0, $max + 1);
            $pos = strrpos($cut, '-');
            $s = $pos > 10 ? substr($cut, 0, $pos) : substr($s, 0, $max);
        }
        return $s === '' ? 'n' : $s;
    }

    /** Lowercase text plus its Latin transliteration, for forgiving search. */
    public static function searchText(string ...$parts): string
    {
        $s = mb_strtolower(trim(preg_replace('/\s+/u', ' ', implode(' ', $parts))), 'UTF-8');
        $latin = self::transliterate($s);
        return $latin === $s ? $s : "$s $latin";
    }

    /** Normalises a search query the same way. */
    public static function searchQuery(string $q): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/u', ' ', $q)), 'UTF-8');
    }
}
