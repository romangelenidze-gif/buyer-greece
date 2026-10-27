<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Schedule::command('quotes:expire')->hourly();

Schedule::command('autocomplete:sync-hits')->everyTenMinutes();

// Ежемесячная чистка и продвижение популярный запросов автокомплита
Schedule::call(function () {
    // 1. Автоматически переводим в постоянные запросы с частыми переходами (hits > 5)
    DB::table('learned_autocomplete')
        ->where('hits', '>', 5)
        ->update(['is_permanent' => 1]);

    // 2. Удаляем временные запросы (is_permanent = 0), которые не искали больше месяца
    DB::table('learned_autocomplete')
        ->where('is_permanent', 0)
        ->where('last_used_at', '<', now()->subMonth())
        ->delete();
})->monthly();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');