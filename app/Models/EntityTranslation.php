<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntityTranslation extends Model
{
    protected $guarded = [];

    private const GREEK_ACCENTS = [
        'ά' => 'α', 'έ' => 'ε', 'ή' => 'η', 'ί' => 'ι', 'ό' => 'ο', 'ύ' => 'υ', 'ώ' => 'ω',
        'ϊ' => 'ι', 'ΐ' => 'ι', 'ϋ' => 'υ', 'ΰ' => 'υ', 'ς' => 'σ', 'ё' => 'е',
    ];

    public static function normalizeKey(string $text): string
    {
        $text = mb_strtolower(trim((string) preg_replace('/\s+/u', ' ', $text)));

        return strtr($text, self::GREEK_ACCENTS);
    }

    public static function hash(string $text): string
    {
        return md5(self::normalizeKey($text));
    }

    public function forLocale(string $locale): string
    {
        $val = match ($locale) {
            'ru' => $this->text_ru ?: ($this->text_en ?: $this->source_text),
            'ka' => $this->text_ka ?: ($this->text_en ?: $this->source_text),
            'en' => $this->text_en ?: $this->source_text,
            default => $this->source_text,
        };

        return self::ucfirstMb($val);
    }

    public static function ucfirstMb(string $text): string
    {
        $text = trim($text);
        if ($text === '') {
            return '';
        }

        return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
    }
}