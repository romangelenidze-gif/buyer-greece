<?php

namespace App\Services;

class SearchClarificationService
{
    /**
     * Проверяет, требуется ли пользователю уточняющий вопрос перед поиском
     */
    public function evaluateClarification(string $userQuery, array $intentAnalysis): array
    {
        $words = explode(' ', trim($userQuery));
        
        // Если у нас точный запрос с указанием бренда/модели/параметров или запрос длиннее 3 слов — ищем сразу!
        if (count($words) >= 3 || !empty($intentAnalysis['brand']) || !empty($intentAnalysis['filters']['price_max'])) {
            return [
                'needs_clarification' => false,
                'question' => null,
                'options' => []
            ];
        }

        // Пример сценария для слишком широких категорий (например: "телефон", "кроссовки")
        $cleanQuery = mb_strtolower(trim($userQuery));

        if (in_array($cleanQuery, ['телефон', 'смартфон', 'ტელეფონი'])) {
            return [
                'needs_clarification' => true,
                'question' => __('Что для вас наиболее важно при выборе смартфона?'),
                'options' => [
                    ['label' => __('Хорошая камера'), 'filter' => 'camera'],
                    ['label' => __('Автономность и батарея'), 'filter' => 'battery'],
                    ['label' => __('Флагманская производительность'), 'filter' => 'flagship'],
                    ['label' => __('Бюджетные варианты до €200'), 'max_price' => 200],
                ]
            ];
        }

        if (in_array($cleanQuery, ['кроссовки', 'обувь', 'ბოტასები'])) {
            return [
                'needs_clarification' => true,
                'question' => __('Для каких целей подбираете кроссовки?'),
                'options' => [
                    ['label' => __('Для бега и спорта'), 'feature' => 'running'],
                    ['label' => __('Повседневная ходьба'), 'feature' => 'casual'],
                    ['label' => __('До €100'), 'max_price' => 100],
                ]
            ];
        }

        return [
            'needs_clarification' => false,
            'question' => null,
            'options' => []
        ];
    }
}