<?php

namespace App\Services;

class CategoryMatcher
{
    private const EXTRA_STOPWORDS = [
        'for', 'the', 'and', 'of', 'in', 'to', 'buy', 'shop', 'online', 'new', 'greece', 'official', 'store',
        'με', 'για', 'και',
    ];

    private const GENDER_PREFIX_LABELS = [
        'men' => 'Мужские',
        'women' => 'Женские',
        'kids' => 'Детские',
    ];

    /** @var array<string, true> */
    private array $stop = [];

    /** @var string[] */
    private array $brandWords = [];

    public function __construct(private FeedCategoryTiles $tiles)
    {
        $glossary = (array) config('search_glossary', []);

        foreach (array_merge((array) ($glossary['stopwords'] ?? []), self::EXTRA_STOPWORDS) as $word) {
            $this->stop[$this->norm((string) $word)] = true;
        }

        foreach ((array) ($glossary['brands'] ?? []) as $alias => $canonical) {
            $this->brandWords[] = $this->norm((string) $alias);
            $this->brandWords[] = $this->norm((string) $canonical);
        }

        $this->brandWords[] = $this->norm((string) config('home_categories.feed', 'adidas'));
        $this->brandWords = array_values(array_unique(array_filter($this->brandWords)));
    }

    public function resolve(string $query): ?string
    {
        $tokens = $this->tokens($query);

        if ($tokens === []) {
            return null;
        }

        $gender = null;
        $type = null;
        $keywordTile = null;

        foreach ($tokens as $token) {
            if (($g = $this->conceptKey($token, 'genders')) !== null) {
                if ($gender !== null && $gender !== $g) {
                    return null;
                }
                $gender = $g;
                continue;
            }

            if (($t = $this->typeKey($token)) !== null) {
                if ($type !== null && $type !== $t) {
                    return null;
                }
                $type = $t;
                continue;
            }

            if (($slug = $this->tileByKeyword($token)) !== null) {
                if ($keywordTile !== null && $keywordTile !== $slug) {
                    return null;
                }
                $keywordTile = $slug;
                continue;
            }

            if ($this->matchesAny($token, $this->compile((array) config('home_categories.concepts.fillers', [])))) {
                continue;
            }

            return null;
        }

        if ($keywordTile !== null) {
            return $gender === null ? $keywordTile : null;
        }

        $tops = (array) config('home_categories.concepts.types', []);

        if ($gender !== null && $type !== null) {
            return $this->tileSlugByWhere(['gender' => $gender, 'category_top' => $tops[$type]['top'] ?? null]);
        }

        if ($gender === null && $type === 'accessories') {
            return $this->tileSlugByWhere(['category_top' => $tops[$type]['top'] ?? null]);
        }

        return null;
    }

    /**
     * @return array<int, array{slug: string, label: string, count: int, url: string}>
     */
    public function popularTiles(int $limit = 5): array
    {
        return $this->tiles->get()->take($limit)->values()->all();
    }

    /**
     * @return array<int, array{slug: string, label: string, count: int, url: string}>
     */
    public function suggest(string $typed, int $limit = 5): array
    {
        $tokens = $this->tokens($typed);

        if ($tokens === []) {
            return [];
        }

        $config = [];
        foreach ((array) config('home_categories.tiles', []) as $tile) {
            $config[$tile['slug']] = $tile;
        }

        // Только категории, реально существующие в БД (count > 0)
        $activeTiles = $this->tiles->get()->all();
        $out = [];

        // 1. Прямое совпадение по активным категориям ("женская одежда", "women's clothing", "sneakers")
        foreach ($activeTiles as $tile) {
            $entries = $this->haystack($tile, $config[$tile['slug']] ?? []);

            foreach ($tokens as $token) {
                if (!$this->prefixMatchesAny($token, $entries)) {
                    continue 2;
                }
            }

            $out[] = $tile;

            if (count($out) >= $limit) {
                return $out;
            }
        }

        // 2. Комбинация Пол + Активная подкатегория ("женские кроссовки", "мужские худи")
        if ($out === [] && count($tokens) >= 2) {
            $gender = null;
            $restTokens = [];

            foreach ($tokens as $token) {
                $g = $this->prefixConceptKey($token, 'genders');
                if ($g !== null && $gender === null) {
                    $gender = $g;
                } else {
                    $restTokens[] = $token;
                }
            }

            if ($gender !== null && $restTokens !== []) {
                $genderWord = __(self::GENDER_PREFIX_LABELS[$gender] ?? '');

                foreach ($activeTiles as $tile) {
                    $tileCfg = $config[$tile['slug']] ?? [];
                    if (isset($tileCfg['where']['gender'])) {
                        continue;
                    }

                    $entries = $this->haystack($tile, $tileCfg);
                    foreach ($restTokens as $rt) {
                        if (!$this->prefixMatchesAny($rt, $entries)) {
                            continue 2;
                        }
                    }

                    $lowerSub = mb_strtolower(mb_substr($tile['label'], 0, 1)) . mb_substr($tile['label'], 1);
                    $combinedLabel = trim($genderWord . ' ' . $lowerSub);

                    $out[] = [
                        'slug' => $gender . '-' . $tile['slug'],
                        'label' => $combinedLabel,
                        'count' => $tile['count'],
                        'url' => route('product-search', ['q' => $combinedLabel]),
                    ];

                    if (count($out) >= $limit) {
                        return $out;
                    }
                }
            }
        }

        return array_slice($out, 0, $limit);
    }

    public function tokens(string $text): array
    {
        $out = [];

        foreach ($this->words($text) as $word) {
            if (mb_strlen($word) < 2 || isset($this->stop[$word]) || $this->isBrand($word)) {
                continue;
            }
            $out[] = $word;
        }

        return $out;
    }

    public function withoutBrands(string $text): string
    {
        $out = [];

        foreach ($this->words($text) as $word) {
            if (!$this->isBrand($word)) {
                $out[] = $word;
            }
        }

        return implode(' ', $out);
    }

    public function isBrand(string $word): bool
    {
        foreach ($this->brandWords as $brand) {
            if ($word === $brand || (mb_strlen($brand) >= 5 && str_starts_with($word, $brand))) {
                return true;
            }
        }

        return false;
    }

    public function norm(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = strtr($text, [
            'ё' => 'е',
            'ά' => 'α', 'έ' => 'ε', 'ή' => 'η', 'ί' => 'ι', 'ό' => 'ο', 'ύ' => 'υ', 'ώ' => 'ω',
            'ϊ' => 'ι', 'ΐ' => 'ι', 'ϋ' => 'υ', 'ΰ' => 'υ', 'ς' => 'σ',
        ]);
        $text = str_replace(["'", '’', '`', 'ʼ'], '', $text);
        $text = (string) preg_replace('/[^\p{L}\p{N}]+/u', ' ', $text);

        return trim((string) preg_replace('/\s+/u', ' ', $text));
    }

    private function words(string $text): array
    {
        $norm = $this->norm($text);

        return $norm === '' ? [] : explode(' ', $norm);
    }

    private function haystack(array $tile, array $config): array
    {
        $entries = [];

        foreach ($this->words((string) ($tile['label'] ?? '')) as $word) {
            $entries[] = ['v' => $word, 'stem' => false];
        }

        $entries = array_merge($entries, $this->compile((array) ($config['keywords'] ?? [])));

        $where = (array) ($config['where'] ?? []);
        $concepts = (array) config('home_categories.concepts', []);

        if (isset($where['gender'], $concepts['genders'][$where['gender']])) {
            $entries = array_merge($entries, $this->compile($concepts['genders'][$where['gender']]));
        }

        if (isset($where['category_top'])) {
            foreach ((array) ($concepts['types'] ?? []) as $type) {
                if (($type['top'] ?? null) === $where['category_top']) {
                    $entries = array_merge($entries, $this->compile((array) $type['words']));
                }
            }
        }

        return $entries;
    }

    private function compile(array $list): array
    {
        $out = [];

        foreach ($list as $item) {
            $item = (string) $item;
            $stem = str_ends_with($item, '*');
            $value = $this->norm(rtrim($item, '*'));

            if ($value !== '') {
                $out[] = ['v' => $value, 'stem' => $stem];
            }
        }

        return $out;
    }

    private function matchesAny(string $token, array $entries): bool
    {
        foreach ($entries as $e) {
            if ($e['stem'] ? str_starts_with($token, $e['v']) : ($token === $e['v'] || $token === $e['v'] . 's')) {
                return true;
            }
        }

        return false;
    }

    private function prefixMatchesAny(string $token, array $entries): bool
    {
        foreach ($entries as $e) {
            if (str_starts_with($e['v'], $token) || ($e['stem'] && str_starts_with($token, $e['v']))) {
                return true;
            }
        }

        return false;
    }

    private function conceptKey(string $token, string $group): ?string
    {
        foreach ((array) config("home_categories.concepts.{$group}", []) as $key => $words) {
            if ($this->matchesAny($token, $this->compile((array) $words))) {
                return (string) $key;
            }
        }

        return null;
    }

    private function prefixConceptKey(string $token, string $group): ?string
    {
        foreach ((array) config("home_categories.concepts.{$group}", []) as $key => $words) {
            if ($this->prefixMatchesAny($token, $this->compile((array) $words))) {
                return (string) $key;
            }
        }

        return null;
    }

    private function typeKey(string $token): ?string
    {
        foreach ((array) config('home_categories.concepts.types', []) as $key => $type) {
            if ($this->matchesAny($token, $this->compile((array) ($type['words'] ?? [])))) {
                return (string) $key;
            }
        }

        return null;
    }

    private function tileByKeyword(string $token): ?string
    {
        foreach ((array) config('home_categories.tiles', []) as $tile) {
            if ($this->matchesAny($token, $this->compile((array) ($tile['keywords'] ?? [])))) {
                return (string) $tile['slug'];
            }
        }

        return null;
    }

    private function tileSlugByWhere(array $where): ?string
    {
        foreach ((array) config('home_categories.tiles', []) as $tile) {
            if (($tile['where'] ?? null) === $where) {
                return (string) $tile['slug'];
            }
        }

        return null;
    }
}