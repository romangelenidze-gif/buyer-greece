<?php

namespace Tests\Feature;

use App\Models\EntityTranslation;
use App\Models\FeedProduct;
use App\Services\Autocomplete\AutocompleteBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CatalogSmokeTest extends TestCase
{
    use RefreshDatabase;

    private string $testSku = 'SMOKE-TEST-999';

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        FeedProduct::query()->create([
            'feed' => 'adidas',
            'sku' => $this->testSku,
            'model_code' => 'SMOKE-MOD-999',
            'brand' => 'ADIDAS',
            'name' => 'ΜΠΟΥΦΑΝ SmokeRunner Pro',
            'price' => 99.90,
            'old_price' => 129.90,
            'discount_percent' => 23,
            'currency' => 'EUR',
            'colour' => 'Core Black / Cloud White',
            'category_path' => 'Ανδρικά, Ρούχα, Μπουφάν',
            'category_top' => 'Ανδρικά',
            'gender' => 'men',
            'image_url' => 'https://example.com/smoke-jacket.jpg',
            'deep_link' => 'https://example.com/out/smoke-jacket',
            'merchant_url' => 'https://adidas.gr/smoke-jacket',
            'in_stock' => true,
            'is_active' => true,
            'sizes' => [
                ['size' => 'M', 'stock' => 3],
                ['size' => 'L', 'stock' => 0],
            ],
            'specs' => [
                'Κανονική εφαρμογή',
                'Κλείσιμο με φερμουάρ',
            ],
            'search_text' => ' μπουφαν smokerunner pro adidas core black cloud white ',
        ]);

        $now = now();
        $translations = [
            ['title_prefix', 'Μπουφάν', 'Jacket', 'Куртка', 'Ქურთუკი'],
            ['color', 'Core Black', 'Core Black', 'Основной чёрный', 'ძირითადი შავი'],
            ['color', 'Cloud White', 'Cloud White', 'Облачно-белый', 'ღრუბლის თეთრი'],
            ['spec', 'Κανονική εφαρμογή', 'Regular fit', 'Классический крой', 'სტანდარტული თარგი'],
            ['spec', 'Κλείσιμο με φερμουάρ', 'Zip closure', 'Застёжка на молнию', 'ელვა-შესაკრავი'],
        ];

        foreach ($translations as [$type, $src, $en, $ru, $ka]) {
            DB::table('entity_translations')->upsert([[
                'entity_type' => $type,
                'source_hash' => EntityTranslation::hash($src),
                'source_text' => $src,
                'text_en' => $en,
                'text_ru' => $ru,
                'text_ka' => $ka,
                'created_at' => $now,
                'updated_at' => $now,
            ]], ['entity_type', 'source_hash'], ['text_en', 'text_ru', 'text_ka', 'updated_at']);
        }
    }

    public function test_georgian_translations_never_use_mtavruli_uppercase(): void
    {
        $row = EntityTranslation::query()
            ->where('entity_type', 'title_prefix')
            ->where('source_hash', EntityTranslation::hash('ΜΠΟΥΦΑΝ'))
            ->firstOrFail();

        $ka = $row->forLocale('ka');

        $this->assertSame('ქურთუკი', $ka);
        $this->assertDoesNotMatchRegularExpression('/[\x{1C90}-\x{1CBF}]/u', $ka);
    }

    public function test_home_page_renders_successfully_in_all_locales(): void
    {
        foreach (['ru', 'ka', 'en'] as $locale) {
            $response = $this->withSession(['app_locale' => $locale])->get(route('home'));

            $response->assertStatus(200);
        }
    }

    public function test_pdp_renders_localized_title_color_specs_and_related_products(): void
    {
        $ruResponse = $this->withSession(['app_locale' => 'ru'])
            ->get(route('feed-product.show', ['feed' => 'adidas', 'sku' => $this->testSku]));

        $ruResponse->assertStatus(200);
        $ruResponse->assertSee('Куртка SmokeRunner Pro');
        $ruResponse->assertSee('Основной чёрный / Облачно-белый');
        $ruResponse->assertSee('Классический крой');
        $ruResponse->assertSee('Застёжка на молнию');

        $kaResponse = $this->withSession(['app_locale' => 'ka'])
            ->get(route('feed-product.show', ['feed' => 'adidas', 'sku' => $this->testSku]));

        $kaResponse->assertStatus(200);
        $kaResponse->assertSee('ქურთუკი SmokeRunner Pro');
        $kaResponse->assertSee('ძირითადი შავი / ღრუბლის თეთრი');
        $kaResponse->assertSee('სტანდარტული თარგი');
    }

    public function test_materialized_autocomplete_rebuild_and_multilingual_lookup(): void
    {
        $stats = app(AutocompleteBuilder::class)->rebuild();

        $this->assertGreaterThan(0, $stats['total']);
        $this->assertDatabaseHas('autocomplete_entries', [
            'entry_key' => 'model:adidas:smoke-mod-999',
            'title_ru' => 'Куртка SmokeRunner Pro',
            'title_ka' => 'ქურთუკი SmokeRunner Pro',
            'title_en' => 'Jacket SmokeRunner Pro',
            'is_active' => true,
        ]);

        $resRu = $this->withSession(['app_locale' => 'ru'])
            ->getJson(route('api.autocomplete', ['q' => 'куртка smokerunner']));

        $resRu->assertStatus(200);
        $resRu->assertJsonFragment([
            'label' => 'Куртка SmokeRunner Pro',
        ]);

        $resKa = $this->withSession(['app_locale' => 'ka'])
            ->getJson(route('api.autocomplete', ['q' => 'ქურთუკი smokerunner']));

        $resKa->assertStatus(200);
        $resKa->assertJsonFragment([
            'label' => 'ქურთუკი SmokeRunner Pro',
        ]);
    }

    public function test_product_search_and_redis_sync_command_do_not_fail(): void
    {
        $searchResponse = $this->withSession(['app_locale' => 'ru'])
            ->get(route('product-search', ['q' => 'SmokeRunner']));

        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Куртка SmokeRunner Pro');

        $this->artisan('autocomplete:sync-hits')
            ->assertExitCode(0);
    }
}