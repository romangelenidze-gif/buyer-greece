<?php

namespace App\Services\Feeds;

final class FeedText
{
    private const ACCENTS = [
        'ά' => 'α', 'έ' => 'ε', 'ή' => 'η', 'ί' => 'ι', 'ό' => 'ο', 'ύ' => 'υ', 'ώ' => 'ω',
        'ϊ' => 'ι', 'ΐ' => 'ι', 'ϋ' => 'υ', 'ΰ' => 'υ', 'ς' => 'σ', 'ё' => 'е',
    ];

    /** Точные слова (проверяются целиком, чтобы 'man' не цеплял обрывки) */
    private const GENDER_EXACT = [
        'kids' => ['kids', 'kid', 'boys', 'boy', 'girls', 'girl', 'children', 'child', 'junior', 'juniors', 'youth', 'infant', 'infants', 'toddler', 'toddlers', 'baby', 'babies'],
        'women' => ['women', 'womens', 'woman', 'ladies', 'lady', 'female'],
        'men' => ['men', 'mens', 'man', 'male'],
    ];

    /** Префиксы основ любой длины (греческий, русский, грузинский) */
    private const GENDER_STEMS = [
        'kids' => ['παιδι', 'βρεφικ', 'εφηβικ', 'αγορ', 'κοριτσ', 'детск', 'детей', 'детям', 'ребен', 'мальчик', 'девочк', 'ბავშვ', 'საბავშვო'],
        'women' => ['γυναικ', 'женск', 'женщин', 'девушк', 'ქალ', 'გოგო'],
        'men' => ['ανδρ', 'αντρ', 'мужск', 'мужчин', 'парн', 'კაც', 'მამაკაც'],
    ];

    private const TOP_CATEGORY_PREFIXES = [
        'Παπούτσια' => ['shoe', 'shoes', 'footwear', 'обув', 'ფეხსაცმ', 'παπουτσ'],
        'Ένδυση'    => ['clothing', 'clothes', 'apparel', 'wear', 'одежд', 'ტანსაცმ', 'ρουχ', 'ενδυσ'],
        'Αξεσουάρ'  => ['accessory', 'accessories', 'аксессуар', 'აქსესუარ', 'αξεσουαρ'],
    ];

    private const SUBCATEGORY_PREFIXES = [
        ['stems' => ['sneaker', 'sneakers', 'trainer', 'trainers', 'кроссовк', 'кед', 'ბოტას', 'კედ'], 'paths' => ['Sneakers']],
        ['stems' => ['running', 'runners', 'бегов', 'бег', 'სარბენ', 'τρεξιμ'], 'paths' => ['Παπούτσια Τρεξίματος']],
        ['stems' => ['football', 'soccer', 'cleats', 'бутс', 'футбольн', 'საფეხბურთო', 'ბუც', 'ποδοσφαιρ'], 'paths' => ['Παπούτσια Ποδοσφαίρου']],
        ['stems' => ['slide', 'slides', 'sandal', 'sandals', 'сланц', 'шлепанц', 'сандал', 'ჩუსტ', 'სანდალ', 'σαγιοναρ', 'παντοφλ'], 'paths' => ['Παντόφλες και σαγιονάρες']],
        ['stems' => ['tracksuit', 'tracksuits', 'костюм', 'კოსტიუმ', 'φορμ'], 'paths' => ['Αθλητικές Φόρμες']],
        ['stems' => ['hoodie', 'hoodies', 'sweatshirt', 'sweatshirts', 'худи', 'толстовк', 'ჰუდ', 'სვიტშოტ', 'φουτερ'], 'paths' => ['Φούτερ Και Ζακέτες']],
        ['stems' => ['jacket', 'jackets', 'coat', 'coats', 'куртк', 'пуховик', 'ქურთუკ', 'μπουφαν'], 'paths' => ['Jackets', 'Μπουφάν']],
        ['stems' => ['tshirt', 'tshirts', 'shirt', 'shirts', 'tee', 'jersey', 'jerseys', 'футболк', 'майк', 'джерси', 'მაისურ', 'ჯერსი', 'φανελ'], 'paths' => ['Φανέλες', 'Μπλούζες']],
        ['stems' => ['short', 'shorts', 'legging', 'leggings', 'шорт', 'легинс', 'леггинс', 'შორტ', 'ლეგინს', 'κολαν'], 'paths' => ['Σορτς', 'Κολάν']],
        ['stems' => ['dress', 'dresses', 'skirt', 'skirts', 'плать', 'юбк', 'კაბ', 'ქვედაბოლო', 'φορεμ', 'φουστ'], 'paths' => ['Φούστες και Φορέματα']],
        ['stems' => ['bag', 'bags', 'backpack', 'backpacks', 'сумк', 'рюкзак', 'ჩანთ', 'ზურგჩანთ', 'τσαντ', 'σακιδι'], 'paths' => ['Τσάντες']],
        ['stems' => ['sock', 'socks', 'носк', 'წინდ', 'καλτσ'], 'paths' => ['Κάλτσες']],
        ['stems' => ['cap', 'caps', 'hat', 'hats', 'beanie', 'кепк', 'шапк', 'ქუდ', 'καπελ', 'σκουφ'], 'paths' => ['Καπέλα', 'Σκούφοι']],
    ];

    private const CATEGORY_LABELS = [
        'παπουτσια' => 'Обувь',
        'ενδυση' => 'Одежда',
        'αξεσουαρ' => 'Аксессуары',
        'sneakers' => 'Кроссовки',
        'παπουτσια τρεξιματοσ' => 'Беговая обувь',
        'παπουτσια ποδοσφαιρου' => 'Футбольные бутсы',
        'παπουτσια γυμναστηριου' => 'Обувь для зала',
        'παντοφλεσ και σαγιοναρεσ' => 'Сланцы и шлёпанцы',
        'αθλητικεσ φορμεσ' => 'Спортивные костюмы',
        'φουτερ και ζακετεσ' => 'Худи и толстовки',
        'μπουφαν' => 'Куртки',
        'jackets' => 'Куртки',
        'φανελεσ' => 'Джерси и футболки',
        'μπλουζεσ' => 'Футболки и лонгсливы',
        'σορτσ' => 'Шорты',
        'κολαν' => 'Леггинсы',
        'φουστεσ και φορεματα' => 'Платья и юбки',
        'τσαντεσ' => 'Сумки и рюкзаки',
        'καλτσεσ' => 'Носки',
        'καπελα' => 'Кепки и шапки',
    ];

    public static function normalize(string $text): string
    {
        $text = strtr(mb_strtolower($text), self::ACCENTS);
        $text = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $text) ?? '';

        return trim(preg_replace('/\s+/u', ' ', $text) ?? '');
    }

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

    public static function detectGender(string $token): ?string
    {
        foreach (['kids', 'women', 'men'] as $gender) {
            if (in_array($token, self::GENDER_EXACT[$gender], true)) {
                return $gender;
            }
            foreach (self::GENDER_STEMS[$gender] as $stem) {
                if (str_starts_with($token, $stem)) {
                    return $gender;
                }
            }
        }

        return null;
    }

    public static function detectTopCategory(string $token): ?string
    {
        foreach (self::TOP_CATEGORY_PREFIXES as $topCategory => $prefixes) {
            if (self::matchesPrefixList($token, $prefixes)) {
                return $topCategory;
            }
        }

        return null;
    }

    /** @return string[]|null */
    public static function detectSubcategoryPaths(string $token): ?array
    {
        foreach (self::SUBCATEGORY_PREFIXES as $entry) {
            if (self::matchesPrefixList($token, $entry['stems'])) {
                return $entry['paths'];
            }
        }

        return null;
    }

    public static function translateCategorySegment(string $rawSegment): string
    {
        $norm = self::normalize($rawSegment);
        if (isset(self::CATEGORY_LABELS[$norm])) {
            return __(self::CATEGORY_LABELS[$norm]);
        }

        return trim($rawSegment);
    }

    public static function parseQuery(string $query, array $feedCfg = [], array $globalCfg = []): array
    {
        $strip = array_map([self::class, 'normalize'], (array) ($feedCfg['strip_words'] ?? []));
        $stop = array_map([self::class, 'normalize'], (array) ($globalCfg['stop_words'] ?? []));
        $prefixes = array_map([self::class, 'normalize'], (array) ($globalCfg['stop_prefixes'] ?? []));

        foreach ((array) config('search_glossary.brands', []) as $alias => $canonical) {
            $strip[] = self::normalize((string) $alias);
            $strip[] = self::normalize((string) $canonical);
        }

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
        $categoryTop = null;
        $categoryPaths = [];
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

            if (($g = self::detectGender($token)) !== null) {
                $gender ??= $g;
                continue;
            }

            if (($top = self::detectTopCategory($token)) !== null) {
                $categoryTop ??= $top;
                continue;
            }

            if (($paths = self::detectSubcategoryPaths($token)) !== null) {
                $categoryPaths = array_values(array_unique(array_merge($categoryPaths, $paths)));
                continue;
            }

            $stem = self::stem($token);
            $groups[] = array_values(array_unique(array_merge([$stem], $synonyms[$stem] ?? [])));
        }

        return [
            'groups' => $groups,
            'gender' => $gender,
            'category_top' => $categoryTop,
            'category_paths' => $categoryPaths,
        ];
    }

    public static function likePattern(string $stem): string
    {
        if (ctype_digit($stem) && mb_strlen($stem) <= 3) {
            return '% ' . $stem . ' %';
        }

        return '% ' . $stem . '%';
    }

    /** @param string[] $prefixes */
    private static function matchesPrefixList(string $token, array $prefixes): bool
    {
        foreach ($prefixes as $prefix) {
            $len = mb_strlen($prefix);
            if ($len <= 4) {
                if ($token === $prefix || $token === $prefix . 's') {
                    return true;
                }
            } elseif (str_starts_with($token, $prefix)) {
                return true;
            }
        }

        return false;
    }
}