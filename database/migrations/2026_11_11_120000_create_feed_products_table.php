<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('feed_products', function (Blueprint $table) {
            $table->id();
            $table->string('feed', 32);                         // ключ фида из config/feeds.php (adidas)
            $table->string('sku', 64);                          // merchant_product_id: одна расцветка модели
            $table->string('model_code', 64)->nullable();       // mpn: общий код модели для всех расцветок
            $table->string('brand', 64)->nullable();
            $table->string('name', 255);
            $table->string('colour', 255)->nullable();
            $table->string('category_path', 500)->nullable();   // «Παπούτσια,Sneakers»
            $table->string('category_top', 64)->nullable();     // Παπούτσια / Ένδυση / Αξεσουάρ
            $table->string('gender', 16)->nullable();           // men | women | unisex | kids
            $table->decimal('price', 10, 2);
            $table->decimal('old_price', 10, 2)->nullable();
            $table->unsignedTinyInteger('discount_percent')->default(0);
            $table->char('currency', 3)->default('EUR');
            $table->decimal('rating', 2, 1)->nullable();        // NULL = оценок нет
            $table->boolean('in_stock')->default(true);         // есть хотя бы один размер
            $table->json('sizes')->nullable();                  // [{size, stock, ean}]
            $table->json('specs')->nullable();                  // характеристики (массив строк)
            $table->text('image_url')->nullable();
            $table->text('deep_link');                          // партнёрская ссылка Awin (даёт комиссию)
            $table->text('merchant_url')->nullable();           // прямая ссылка на магазин (без комиссии)
            $table->decimal('delivery_cost', 6, 2)->nullable();
            $table->text('search_text');                        // нормализованный текст для поиска
            $table->boolean('is_active')->default(true);        // false = товар пропал из фида
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->unique(['feed', 'sku']);
            $table->index(['feed', 'is_active', 'model_code']);
            $table->index(['feed', 'is_active', 'price']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_products');
    }
};
