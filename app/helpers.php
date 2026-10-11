<?php

use App\Models\PageContent;
use Illuminate\Support\Facades\Cache;

if (!function_exists('page_text')) {
    /**
     * Получить динамический текст страницы из базы данных по ключу.
     * Если запись отсутствует в БД, возвращает значение по умолчанию.
     *
     * @param string $key Уникальный ключ (например, 'home.hero_title')
     * @param string $default Дефолтный текст
     * @return string
     */
    function page_text(string $key, string $default = ''): string
    {
        return Cache::rememberForever("page_text_{$key}", function () use ($key, $default) {
            return PageContent::where('key', $key)->value('value') ?? $default;
        });
    }
}