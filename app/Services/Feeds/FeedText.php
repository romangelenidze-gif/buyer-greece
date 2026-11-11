<?php

namespace App\Services\Feeds;

/**
 * Общая нормализация текста для импорта и поиска. Одна и та же функция на записи и на чтении,
 * поэтому «Παπούτσια», «ΠΑΠΟΥΤΣΙΑ» и «παπουτσια» дают одинаковый результат.
 */
final class FeedText
{
    private const ACCENTS = [
        'ά' => 'α', 'έ' => 'ε', 'ή' => 'η', 'ί' => 'ι', 'ό' => 'ο', 'ύ' => 'υ', 'ώ' => 'ω',
        'ϊ' => 'ι', 'ΐ' => 'ι', 'ϋ' => 'υ', 'ΰ' => 'υ', 'ς' => 'σ', 'ё' => 'е',
    ];

    private const MEN_WORDS = ['men', 'mens', 'man', 'male'];
    private const WOMEN_WORDS = ['women', 'womens', 'woman', 'ladies', 'female'];
    private const KIDS_WORDS = ['kids', 'kid', 'boys', 'boy', 'girls', 'girl', 'children', 'child', 'junior', 'juniors', 'toddler'];

    /** нижний регистр, без греческих ударений и знаков препинания, одиночные пробелы */
    public static function normalize(string $text): string
    {
        $text = strtr(mb_strtolower($text), self::ACCENTS);
        $text = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $text) ?? '';

        return trim(preg_replace('/\s+/u', ' ', $text) ?? '');
    }

    /**
     * Грубая основа слова. В греческом окончания меняются (παπούτσι / παπούτσια / παπούτσιών),
     * поэтому берём начало слова. В английском отрезаем множественное «s».
     */
    public static function stem(string $token): string
    {
        $len = mb_strlen($token);

        if (preg_match('/^\p{Greek}+$/u', $token)) {
            if ($len >= 7) {
                return mb_substr($token, 0, 5);
            }

            if ($len >= 5) {
                return mb_substr($token, 0, 4);
            }

            return $token;
        }

        if ($len > 4 && preg_match('/^[a-z]+$/', $token) && str_ends_with($token, 's')) {
            return substr($token, 0, -1);
        }

        return $token;
    }

    /** men | women | kids | null */
    public static function detectGender(string $token): ?string
    {
        if (str_starts_with($token, 'ανδρ') || in_array($token, self::MEN_WORDS, true)) {
            return 'men';
        }

        if (str_starts_with($token, 'γυναικ') || in_array($token, self::WOMEN_WORDS, true)) {
            return 'women';
        }

        if (str_starts_with($token, 'παιδι') || in_array($token, self::KIDS_WORDS, true)) {
            return 'kids';
        }

        return null;
    }

    /**
     * Разбор поисковой строки в условия: список групп (внутри группы ИЛИ, между группами И) и пол.
     * Слова бренда, стоп-слова и греческое «αθλητικά» отбрасываются: весь каталог и так спортивный Adidas.
     *
     * @return array{groups: array<int, string[]>, gender: ?string}
     */
    public static function parseQuery(string $query, array $feedCfg, array $globalCfg): array
    {
        $strip = array_map([self::class, 'normalize'], (array) ($feedCfg['strip_words'] ?? []));
        $stop = array_map([self::class, 'normalize'], (array) ($globalCfg['stop_words'] ?? []));
        $prefixes = array_map([self::class, 'normalize'], (array) ($globalCfg['stop_prefixes'] ?? []));

        $synonyms = [];
        foreach ((array) ($globalCfg['synonyms'] ?? []) as $word => $alts) {
            $key = self::stem(self::normalize((string) $word));
            $synonyms[$key] = array_values(array_unique(array_filter(array_map(
                fn ($alt) => self::stem(self::normalize((string) $alt)),
                (array) $alts
            ))));
        }

        $normalized = self::normalize($query);

        foreach ((array) ($globalCfg['phrases'] ?? []) as $phrase => $replacement) {
            $normalized = str_replace(self::normalize((string) $phrase), self::normalize((string) $replacement), $normalized);
        }

        $gender = null;
        $groups = [];

        foreach (explode(' ', $normalized) as $token) {
            if ($token === '' || (mb_strlen($token) < 2 && !ctype_digit($token))) {
                continue;
            }

            if (in_array($token, $strip, true) || in_array($token, $stop, true)) {
                continue;
            }

            foreach ($prefixes as $prefix) {
                if ($prefix !== '' && str_starts_with($token, $prefix)) {
                    continue 2;
                }
            }

            $g = self::detectGender($token);

            if ($g !== null) {
                $gender ??= $g;
                continue;
            }

            $stem = self::stem($token);
            $groups[] = array_values(array_unique(array_merge([$stem], $synonyms[$stem] ?? [])));
        }

        return ['groups' => $groups, 'gender' => $gender];
    }

    /**
     * LIKE-шаблон для одной основы: целое слово для коротких чисел (чтобы «5» не находило любой код с пятёркой),
     * иначе начало слова. Текст поиска в базе хранится с пробелами по краям.
     */
    public static function likePattern(string $stem): string
    {
        if (ctype_digit($stem) && mb_strlen($stem) <= 3) {
            return '% ' . $stem . ' %';
        }

        return '% ' . $stem . '%';
    }
}
