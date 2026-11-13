<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('autocomplete_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_key', 191)->unique(); // brand:adidas | cat:sneakers | facet:adidas:sneakers | model:adidas:JI0188
            $table->string('type', 16);                 // brand | category | facet | model
            $table->string('title_en', 255);
            $table->string('title_ru', 255);
            $table->string('title_ka', 255);
            $table->string('title_el', 255);
            $table->string('badge_en', 64)->nullable();
            $table->string('badge_ru', 64)->nullable();
            $table->string('badge_ka', 64)->nullable();
            $table->string('badge_el', 64)->nullable();
            $table->text('search_keywords');            // нормализованные слова и алиасы на всех 4 языках (с пробелами по краям)
            $table->string('url', 500);
            $table->text('image_url')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->unsignedInteger('products_count')->default(1);
            $table->unsignedInteger('popularity_hits')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'type', 'popularity_hits', 'products_count'], 'idx_ac_active_rank');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('autocomplete_entries');
    }
};