<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('slug', 64)->unique();
            $table->string('gender', 16)->nullable(); // men | women | kids | unisex | null
            $table->string('name_en', 128);
            $table->string('name_ru', 128);
            $table->string('name_ka', 128);
            $table->string('name_el', 128)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('feed_category_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('feed', 32);
            $table->char('path_hash', 32); // md5(mb_strtolower(raw_category_path))
            $table->string('raw_category_path', 500);
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('gender_override', 16)->nullable();
            $table->boolean('is_verified')->default(false); // true = проверено админом в Filament
            $table->timestamps();

            $table->unique(['feed', 'path_hash']);
            $table->index(['feed', 'category_id']);
        });

        Schema::create('entity_translations', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 32); // spec | category_segment | color | title_prefix
            $table->char('source_hash', 32);   // md5(mb_strtolower(trim(source_text)))
            $table->string('source_text', 500);
            $table->string('text_en', 500)->nullable();
            $table->string('text_ru', 500)->nullable();
            $table->string('text_ka', 500)->nullable();
            $table->timestamps();

            $table->unique(['entity_type', 'source_hash']);
        });

        Schema::table('feed_products', function (Blueprint $table) {
            if (!Schema::hasColumn('feed_products', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('category_top');
                $table->index(['is_active', 'in_stock', 'category_id', 'gender'], 'idx_fp_catalog_filter');
                $table->index(['is_active', 'in_stock', 'gender', 'category_top'], 'idx_fp_gender_top');
            }
        });

        $this->seedDefaultCategories();
    }

    public function down(): void
    {
        Schema::table('feed_products', function (Blueprint $table) {
            if (Schema::hasColumn('feed_products', 'category_id')) {
                $table->dropIndex('idx_fp_catalog_filter');
                $table->dropIndex('idx_fp_gender_top');
                $table->dropColumn('category_id');
            }
        });

        Schema::dropIfExists('entity_translations');
        Schema::dropIfExists('feed_category_mappings');
        Schema::dropIfExists('categories');
    }

    private function seedDefaultCategories(): void
    {
        $now = now();
        $rows = [
            ['slug' => 'sneakers',       'gender' => null, 'name_en' => 'Sneakers',            'name_ru' => 'Кроссовки',          'name_ka' => 'ბოტასები',             'name_el' => 'Sneakers',                  'sort_order' => 10],
            ['slug' => 'running-shoes',  'gender' => null, 'name_en' => 'Running shoes',       'name_ru' => 'Беговая обувь',      'name_ka' => 'სარბენი ფეხსაცმელი',   'name_el' => 'Παπούτσια Τρεξίματος',      'sort_order' => 20],
            ['slug' => 'football-boots', 'gender' => null, 'name_en' => 'Football boots',      'name_ru' => 'Футбольные бутсы',   'name_ka' => 'საფეხბურთო ბუცები',    'name_el' => 'Παπούτσια Ποδοσφαίρου',     'sort_order' => 30],
            ['slug' => 'training-shoes', 'gender' => null, 'name_en' => 'Gym & training shoes','name_ru' => 'Обувь для зала',     'name_ka' => 'სავარჯიშო ფეხსაცმელი', 'name_el' => 'Παπούτσια Γυμναστηρίου',    'sort_order' => 40],
            ['slug' => 'slides',         'gender' => null, 'name_en' => 'Slides & sandals',    'name_ru' => 'Сланцы и шлёпанцы',  'name_ka' => 'ჩუსტები და სანდლები',  'name_el' => 'Παντόφλες και σαγιονάρες',  'sort_order' => 50],
            ['slug' => 'tracksuits',     'gender' => null, 'name_en' => 'Tracksuits',          'name_ru' => 'Спортивные костюмы', 'name_ka' => 'სპორტული კოსტიუმები',  'name_el' => 'Αθλητικές Φόρμες',          'sort_order' => 60],
            ['slug' => 'hoodies',        'gender' => null, 'name_en' => 'Hoodies & sweatshirts','name_ru' => 'Худи и толстовки',  'name_ka' => 'ჰუდები და სვიტშოტები', 'name_el' => 'Φούτερ Και Ζακέτες',        'sort_order' => 70],
            ['slug' => 'jackets',        'gender' => null, 'name_en' => 'Jackets & coats',     'name_ru' => 'Куртки',             'name_ka' => 'ქურთუკები',            'name_el' => 'Μπουφάν',                   'sort_order' => 80],
            ['slug' => 't-shirts',       'gender' => null, 'name_en' => 'T-shirts & jerseys',  'name_ru' => 'Футболки и джерси',  'name_ka' => 'მაისურები და ჯერსები', 'name_el' => 'Μπλούζες και Φανέλες',      'sort_order' => 90],
            ['slug' => 'shorts',         'gender' => null, 'name_en' => 'Shorts & leggings',   'name_ru' => 'Шорты и леггинсы',   'name_ka' => 'შორტები და ლეგინსები', 'name_el' => 'Σορτς και Κολάν',           'sort_order' => 100],
            ['slug' => 'dresses',        'gender' => null, 'name_en' => 'Dresses & skirts',    'name_ru' => 'Платья и юбки',      'name_ka' => 'კაბები და ქვედაბოლოები','name_el' => 'Φούστες και Φορέματα',     'sort_order' => 110],
            ['slug' => 'bags',           'gender' => null, 'name_en' => 'Bags & backpacks',    'name_ru' => 'Сумки и рюкзаки',    'name_ka' => 'ჩანთები და ზურგჩანთები','name_el' => 'Τσάντες',                  'sort_order' => 120],
            ['slug' => 'accessories',    'gender' => null, 'name_en' => 'Accessories',         'name_ru' => 'Аксессуары',         'name_ka' => 'აქსესუარები',          'name_el' => 'Αξεσουάρ',                  'sort_order' => 130],
        ];

        foreach ($rows as &$row) {
            $row['is_active'] = true;
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }

        DB::table('categories')->insertOrIgnore($rows);
    }
};