<?php

namespace App\Services\Feeds;

/**
 * Строит партнёрскую ссылку Awin на нужную страницу магазина.
 *
 * Ссылка из фида (pclick.php?p=...&a=...&m=...) всегда ведёт на греческую страницу из merchant_deep_link.
 * Чтобы открыть другую страницу (например, английскую версию), используется формат Awin для произвольной
 * целевой страницы: cread.php?awinmid=<m>&awinaffid=<a>&ued=<адрес страницы>. Номера m и a берутся
 * из той же ссылки фида, поэтому отслеживание остаётся вашим.
 */
final class AwinLink
{
    /**
     * @param array{find?: ?string, replace?: ?string} $english правило замены адреса (config feeds.<фид>.english_url)
     */
    public static function forProduct(string $deepLink, ?string $merchantUrl, array $english = []): string
    {
        $find = (string) ($english['find'] ?? '');
        $replace = (string) ($english['replace'] ?? '');

        if ($find === '' || $replace === '' || !$merchantUrl || !str_starts_with($merchantUrl, $find)) {
            return $deepLink;
        }

        parse_str((string) parse_url($deepLink, PHP_URL_QUERY), $query);

        if (empty($query['m']) || empty($query['a'])) {
            return $deepLink;
        }

        $destination = $replace . substr($merchantUrl, strlen($find));

        return 'https://www.awin1.com/cread.php?' . http_build_query([
            'awinmid' => $query['m'],
            'awinaffid' => $query['a'],
            'ued' => $destination,
        ]);
    }
}
