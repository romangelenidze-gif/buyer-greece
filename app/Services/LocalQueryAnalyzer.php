<?php

namespace App\Services;

/**
 * Разбор запроса БЕЗ LLM (бесплатно, мгновенно, детерминированно).
 *
 * Возвращает анализ в том же формате, что GroqAiService::analyzeQuery(), либо null, если запрос
 * нельзя разобрать уверенно (тогда менеджер отправляет его в LLM). Когда сомневаемся, всегда null.
 *
 * Что разбирается локально:
 *  1. Бюджет и сортировка («до 80 евро», «от 50 до 100 €», «дешевле 700$», «самые дешёвые»).
 *  2. Греческие запросы из 2-4 слов и английские запросы с моделью/брендом: уходят в BestPrice как есть.
 *  3. Русские запросы, где КАЖДОЕ слово есть в словаре config/search_glossary.php и запрос достаточно
 *     конкретный (категория + пол, бренд или модель).
 *
 * Грузинский и слишком общие запросы («часы», «кроссовки») всегда идут в LLM.
 */
class LocalQueryAnalyzer
{
    protected const MAX_PASSTHROUGH_TOKENS = 4;

    // Греческие формы «мужской / женский / детский» по роду и числу главного слова (ms fs ns mp fp np).
    protected const ADJECTIVES = [
        'm' => ['ms' => 'ανδρικός', 'fs' => 'ανδρική', 'ns' => 'ανδρικό', 'mp' => 'ανδρικοί', 'fp' => 'ανδρικές', 'np' => 'ανδρικά'],
        'f' => ['ms' => 'γυναικείος', 'fs' => 'γυναικεία', 'ns' => 'γυναικείο', 'mp' => 'γυναικείοι', 'fp' => 'γυναικείες', 'np' => 'γυναικεία'],
        'k' => ['ms' => 'παιδικός', 'fs' => 'παιδική', 'ns' => 'παιδικό', 'mp' => 'παιδικοί', 'fp' => 'παιδικές', 'np' => 'παιδικά'],
    ];

    /** @var string[] */
    protected array $stop = [];

    /** @var array<string, string> русское написание бренда => каноническое */
    protected array $brandMap = [];

    /** @var array<string, string> lower(brand) => canonical brand (для латиницы) */
    protected array $brandLookup = [];

    /** @var array<string, string[]> */
    protected array $genders = [];

    /** @var array<int, array> */
    protected array $categories = [];

    public function __construct()
    {
        $glossary = (array) config('search_glossary', []);

        $this->stop = array_map([$this, 'norm'], (array) ($glossary['stopwords'] ?? []));

        foreach ((array) ($glossary['genders'] ?? []) as $key => $stems) {
            $this->genders[(string) $key] = array_map([$this, 'norm'], (array) $stems);
        }

        foreach ((array) ($glossary['brands'] ?? []) as $ru => $canonical) {
            $this->brandMap[$this->norm((string) $ru)] = (string) $canonical;
            $this->brandLookup[mb_strtolower((string) $canonical)] = (string) $canonical;
        }

        foreach ((array) ($glossary['categories'] ?? []) as $row) {
            $category = $this->expandCategory((array) $row);

            if ($category !== null) {
                $this->categories[] = $category;
            }
        }
    }

    /**
     * Строка словаря [основы, el, en, грамматика, пол, флаги] -> рабочая запись с готовыми формами пола.
     */
    protected function expandCategory(array $row): ?array
    {
        if (!isset($row[0], $row[1], $row[2])) {
            return null;
        }

        $stems = array_values(array_filter(array_map(
            fn ($stem) => $this->norm((string) $stem),
            explode('|', (string) $row[0])
        )));

        $el = (string) $row[1];
        $grammar = (string) ($row[3] ?? 'ns');
        $genders = (string) ($row[4] ?? '');
        $flags = (string) ($row[5] ?? '');

        $forms = [];

        foreach (str_split($genders) as $g) {
            if (isset(self::ADJECTIVES[$g][$grammar])) {
                $forms[$g] = self::ADJECTIVES[$g][$grammar] . ' ' . $el;
            }
        }

        if ($stems === []) {
            return null;
        }

        return [
            'stems' => $stems,
            'el' => $el,
            'en' => (string) $row[2],
            'gender' => $forms,
            'apparel' => str_contains($flags, 'a'),
            'compat' => str_contains($flags, 'c'),
            'model_only' => str_contains($flags, 'm'),
        ];
    }

    protected function norm(string $word): string
    {
        return str_replace('ё', 'е', mb_strtolower(trim($word)));
    }

    public function analyze(string $query): ?array
    {
        $query = trim((string) preg_replace('/\s+/u', ' ', $query));
        $lang = GroqAiService::detectLanguage($query);

        if ($query === '' || $lang === 'ka') {
            return null;
        }

        $price = ['min' => null, 'max' => null, 'sort' => null];
        $rest = $query;

        if (in_array($lang, ['ru', 'en'], true)) {
            [$rest, $price] = $this->extractPriceAndSort($query);
        }

        $tokens = $this->tokenize($rest);

        if ($tokens === []) {
            return null;
        }

        // После удаления бюджета ("iphone 15 до 800 евро" -> "iphone 15") русских слов может не остаться.
        $restLang = GroqAiService::detectLanguage($rest);

        $resolved = $restLang === 'ru'
            ? $this->resolveRussian($tokens)
            : $this->resolvePassthrough($tokens, $restLang);

        if ($resolved === null) {
            return null;
        }

        return [
            'intent' => 'product_search',
            'source_language' => $lang,
            'product_type_en' => $resolved['type_en'],
            'query_el' => $resolved['el'],
            'query_en' => $resolved['en'],
            'relaxed_el' => $resolved['relaxed'],
            'preferred_language' => $resolved['preferred'],
            'greek_query' => $resolved['el'],
            'brand' => $resolved['brand'],
            'is_broad' => false,
            'clarification' => null,
            'filters' => [
                'price_min' => $price['min'],
                'price_max' => $price['max'],
                'sort' => $price['sort'] ?? 'relevance',
                'required_features' => [],
            ],
            'degraded' => false,
            'source' => 'local',
            'model' => null,
        ];
    }

    // ------------------------------------------------------------------ //
    //  Греческий / английский: отправляем как есть
    // ------------------------------------------------------------------ //

    protected function resolvePassthrough(array $tokens, string $lang): ?array
    {
        $count = count($tokens);

        if ($count > self::MAX_PASSTHROUGH_TOKENS) {
            return null;
        }

        $hasDigit = (bool) preg_grep('/\d/', $tokens);
        $words = [];
        $brands = [];

        foreach ($tokens as $token) {
            $canonical = $this->brandLookup[mb_strtolower($token)] ?? null;

            if ($canonical !== null) {
                $brands[] = $canonical;
                $words[] = $canonical;
            } else {
                $words[] = $token;
            }
        }

        // Греческий: 2-4 слова (или есть цифры) считаем достаточно конкретным.
        // Английский: только если есть бренд или модель с цифрами; общие слова ("sneakers") решает LLM.
        $specific = $lang === 'el'
            ? ($count >= 2 || $hasDigit)
            : ($hasDigit || $brands !== []);

        if (!$specific) {
            return null;
        }

        $query = implode(' ', $words);

        return [
            'el' => $query,
            'en' => $query,
            'relaxed' => $query,
            'brand' => $brands[0] ?? null,
            'type_en' => '',
            'preferred' => $lang === 'en' ? 'en' : 'el',
        ];
    }

    // ------------------------------------------------------------------ //
    //  Русский: только по словарю
    // ------------------------------------------------------------------ //

    protected function resolveRussian(array $tokens): ?array
    {
        $stop = $this->stop;
        $brandMap = $this->brandMap;

        $category = null;
        $gender = null;
        $latin = [];
        $brandNames = [];

        foreach ($tokens as $token) {
            $lower = $this->norm($token);

            if (in_array($lower, $stop, true)) {
                continue;
            }

            $g = $this->matchGender($lower);

            if ($g !== null) {
                if ($gender !== null && $gender !== $g) {
                    return null;
                }

                $gender = $g;
                continue;
            }

            $cat = $this->matchCategory($lower);

            if ($cat !== null) {
                if ($category !== null) {
                    return null; // две категории в одном запросе: пусть разбирает LLM
                }

                $category = $cat;
                continue;
            }

            if (isset($brandMap[$lower])) {
                $latin[] = $brandMap[$lower];
                $brandNames[] = $brandMap[$lower];
                continue;
            }

            if (preg_match('/\p{Cyrillic}/u', $token)) {
                return null; // неизвестное русское слово
            }

            $canonical = $this->brandLookup[$lower] ?? null;

            if ($canonical !== null) {
                $latin[] = $canonical;
                $brandNames[] = $canonical;
            } else {
                $latin[] = $token;
            }
        }

        if ($category === null) {
            return null;
        }

        // Размеры одежды/обуви в каталоге не ищутся: число 42 путается с названием модели (Nike Pegasus 42).
        if (!empty($category['apparel'])) {
            $latin = array_values(array_filter($latin, function ($t) {
                if (preg_match('/^\d{2}$/', $t) && (int) $t >= 20 && (int) $t <= 56) {
                    return false;
                }

                return !preg_match('/^(?:xxs|xs|s|m|l|xl|xxl|xxxl)$/i', $t);
            }));
        }

        $elWithGender = (string) $category['el'];

        if ($gender !== null) {
            $forms = (array) ($category['gender'] ?? []);

            if (!isset($forms[$gender])) {
                return null;
            }

            $elWithGender = (string) $forms[$gender];
        }

        // Слишком общий запрос ("кроссовки", "часы"): нужен пол, бренд или модель, иначе решает LLM (уточнение).
        if ($gender === null && $latin === []) {
            return null;
        }

        $latinStr = implode(' ', $latin);
        $hasDigit = (bool) preg_grep('/\d/', $latin);

        if (!empty($category['model_only']) && $hasDigit) {
            $el = $en = $latinStr;
            $preferred = 'en';
        } else {
            $genderEn = ['m' => "men's", 'f' => "women's", 'k' => 'kids'][$gender] ?? '';
            $el = trim($elWithGender . ' ' . $latinStr);
            $en = trim($genderEn . ' ' . $category['en'] . ' ' . $latinStr);
            $preferred = 'el';
        }

        return [
            'el' => $el,
            'en' => $en,
            'relaxed' => trim($category['el'] . ' ' . ($brandNames[0] ?? '')),
            'brand' => !empty($category['compat']) ? null : ($brandNames[0] ?? null),
            'type_en' => (string) $category['en'],
            'preferred' => $preferred,
        ];
    }

    protected function matchGender(string $lower): ?string
    {
        foreach ($this->genders as $key => $stems) {
            if ($this->matchesStems($lower, $stems)) {
                return (string) $key;
            }
        }

        return null;
    }

    protected function matchCategory(string $lower): ?array
    {
        foreach ($this->categories as $category) {
            if ($this->matchesStems($lower, $category['stems'])) {
                return $category;
            }
        }

        return null;
    }

    protected function matchesStems(string $token, array $stems): bool
    {
        foreach ($stems as $stem) {
            if ($token === $stem) {
                return true;
            }

            if (mb_strlen($stem) >= 5 && str_starts_with($token, $stem)) {
                return true;
            }
        }

        return false;
    }

    protected function tokenize(string $text): array
    {
        $parts = preg_split('/[^\p{L}\p{N}\'\-\.]+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $tokens = [];

        foreach ($parts as $part) {
            $part = trim($part, ".-'");

            if ($part !== '') {
                $tokens[] = $part;
            }
        }

        return $tokens;
    }

    // ------------------------------------------------------------------ //
    //  Бюджет и сортировка
    // ------------------------------------------------------------------ //

    /**
     * @return array{0: string, 1: array{min: ?float, max: ?float, sort: ?string}}
     */
    protected function extractPriceAndSort(string $text): array
    {
        $price = ['min' => null, 'max' => null, 'sort' => null];

        // Притяжательные (possessive) квантификаторы не дают регулярке «откусить» часть числа ("до 30 кг" -> 3).
        // Диапазон требует слова «от»: иначе размер перед бюджетом ("42 до 80 евро") принялся бы за нижнюю границу.
        $num = '(\d{1,6}+(?:[.,]\d{1,2}+)?+)(?!\d)';
        $cur = '(€|eur\b|euro\b|евро|\$|usd\b|долл\w*|₽|руб\w*|rub\b|₾|лари|gel\b)';
        // Число с единицей измерения/возраста ("до 3 лет", "до 30 кг", "256 гб") бюджетом не считается.
        $unit = '(?!\s*(?:лет|года|год|мес\w*|кг|kg|гб|gb|тб|tb|см|cm|мм|mm|дюйм\w*|inch|л\b|м\b|шт|mah|мач))';
        $not = '(?<!\p{L})';

        $range = '~' . $not . '(?:от|from)\s*' . $num . '\s*' . $cur . '?\s*(?:-|–|—|до|to)\s*' . $num . '\s*' . $cur . '~iu';
        $max = '~' . $not . '(?:до|дешевле|не\s+дороже|не\s+более|не\s+больше|максимум|under|below|up\s+to|less\s+than|max)\s*' . $num . '\s*' . $cur . '?' . $unit . '~iu';
        $min = '~' . $not . '(?:от|дороже|не\s+менее|не\s+меньше|минимум|over|above|from|more\s+than|min)\s*' . $num . '\s*' . $cur . '?' . $unit . '~iu';

        $text = (string) preg_replace_callback($range, function (array $m) use (&$price) {
            $curB = $this->currencyCode($m[4] ?? '') ?? 'EUR';
            $curA = $this->currencyCode($m[2] ?? '') ?? $curB;

            $a = CurrencyConverter::toEur($this->toFloat($m[1]), $curA);
            $b = CurrencyConverter::toEur($this->toFloat($m[3]), $curB);

            if ($a === null || $b === null) {
                return $m[0];
            }

            $price['min'] = min($a, $b);
            $price['max'] = max($a, $b);

            return ' ';
        }, $text, 1);

        $text = (string) preg_replace_callback($max, function (array $m) use (&$price) {
            $value = $this->singlePrice($m);

            if ($value === null) {
                return $m[0];
            }

            $price['max'] ??= $value;

            return ' ';
        }, $text, 1);

        $text = (string) preg_replace_callback($min, function (array $m) use (&$price) {
            $value = $this->singlePrice($m);

            if ($value === null) {
                return $m[0];
            }

            $price['min'] ??= $value;

            return ' ';
        }, $text, 1);

        $asc = '~' . $not . '(?:(?:самы[йеих]\s+)?(?:дешёв|дешев)\p{L}*|подешевле|cheapest|cheap)(?!\p{L})~iu';
        $desc = '~' . $not . '(?:(?:самы[йеих]\s+)?дорог\p{L}*|most\s+expensive)(?!\p{L})~iu';

        if (preg_match($asc, $text)) {
            $price['sort'] = 'price_asc';
            $text = (string) preg_replace($asc, ' ', $text);
        } elseif (preg_match($desc, $text)) {
            $price['sort'] = 'price_desc';
            $text = (string) preg_replace($desc, ' ', $text);
        }

        if ($price['min'] !== null && $price['max'] !== null && $price['min'] > $price['max']) {
            [$price['min'], $price['max']] = [$price['max'], $price['min']];
        }

        return [trim((string) preg_replace('/\s+/u', ' ', $text)), $price];
    }

    protected function singlePrice(array $m): ?float
    {
        $cur = $this->currencyCode($m[2] ?? '');
        $number = $this->toFloat($m[1]);

        // Без значка валюты принимаем только "настоящие" суммы: "до 3" скорее возраст/количество.
        if ($cur === null && $number < 10) {
            return null;
        }

        return CurrencyConverter::toEur($number, $cur ?? 'EUR');
    }

    protected function toFloat(string $raw): float
    {
        return (float) str_replace(',', '.', $raw);
    }

    protected function currencyCode(string $raw): ?string
    {
        $c = mb_strtolower(trim($raw));

        if ($c === '') {
            return null;
        }

        if ($c === '€' || str_starts_with($c, 'eur') || str_starts_with($c, 'евро')) {
            return 'EUR';
        }

        if ($c === '$' || str_starts_with($c, 'usd') || str_starts_with($c, 'долл')) {
            return 'USD';
        }

        if ($c === '₽' || str_starts_with($c, 'руб') || str_starts_with($c, 'rub')) {
            return 'RUB';
        }

        if ($c === '₾' || str_starts_with($c, 'лари') || str_starts_with($c, 'gel')) {
            return 'GEL';
        }

        return null;
    }
}
