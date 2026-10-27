<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('learned_autocomplete', function (Blueprint $table) {
            $table->id();
            $table->string('query')->unique(); // Уникальный поисковый запрос
            $table->json('suggestions_json');    // Готовый массив подсказок от ИИ
            $table->unsignedInteger('hits')->default(1); // Счетчик популярности
            $table->boolean('is_permanent')->default(0); // Статус постоянного (неудаляемого)
            $table->timestamp('last_used_at')->nullable(); // Дата последнего использования
            $table->timestamps();
            $table->index(['query', 'hits']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learned_autocomplete');
    }
};