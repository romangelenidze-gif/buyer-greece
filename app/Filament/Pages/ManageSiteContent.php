<?php

namespace App\Filament\Pages;

use App\Models\PageContent;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use UnitEnum;

class ManageSiteContent extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Контент страниц';
    protected static ?string $title = 'Управление текстами сайта';
    protected static UnitEnum|string|null $navigationGroup = 'Настройки сайта';

    protected string $view = 'filament.pages.manage-site-content';

    public ?array $data = [];

    public function mount(): void
    {
        $savedData = PageContent::pluck('value', 'key')->toArray();
        
        // Преобразуем плоские ключи ('home.hero_title') во вложенный массив для корректного зажима в форму
        $formattedData = [];
        foreach ($savedData as $key => $value) {
            Arr::set($formattedData, $key, $value);
        }

        $this->form->fill($formattedData);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('PagesContent')->tabs([
                    
                    // Вкладка: Главная
                    Tab::make('Главная страница (Home)')->schema([
                        Section::make('Hero Блок')->schema([
                            TextInput::make('home.hero_title')
                                ->label('Заголовок Hero-блока')
                                ->default('Доставка и выкуп любых товаров из магазинов Греции'),
                            Textarea::make('home.hero_subtitle')
                                ->label('Подзаголовок Hero-блока')
                                ->rows(3)
                                ->default('Мы помогаем выкупать товары или предоставляем греческий адрес для самостоятельных покупок.'),
                        ]),
                    ]),

                    // Вкладка: Как это работает
                    Tab::make('Как это работает')->schema([
                        Section::make('Сценарии и этапы')->schema([
                            Textarea::make('how_it_works.scenario_1')
                                ->label('Сценарий 1: Выкупите за меня')
                                ->rows(3),
                            Textarea::make('how_it_works.scenario_2')
                                ->label('Сценарий 2: Форвардинг')
                                ->rows(3),
                            Textarea::make('how_it_works.scenario_3')
                                ->label('Сценарий 3: Передача Camex')
                                ->rows(3),
                        ]),
                    ]),

                    // Вкладка: Форвардинг
                    Tab::make('Форвардинг')->schema([
                        Section::make('Правила и инструкции')->schema([
                            Textarea::make('forwarding.hero_subtitle')
                                ->label('Подзаголовок страницы Форвардинга')
                                ->rows(3),
                            Textarea::make('forwarding.rules')
                                ->label('Правила приема самостоятельных заказов')
                                ->rows(4),
                        ]),
                    ]),

                    // Вкладка: Центр помощи
                    Tab::make('Центр помощи (Help)')->schema([
                        Section::make('Заголовки и описания')->schema([
                            TextInput::make('help.title')
                                ->label('Заголовок страницы Help')
                                ->default('Центр помощи'),
                            Textarea::make('help.subtitle')
                                ->label('Подзаголовок страницы Help')
                                ->rows(2),
                        ]),
                    ]),
                ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        // Преобразуем многомерный массив состояния в плоский с разделением точками ('home.hero_title' => 'val')
        $dotState = Arr::dot($state);

        foreach ($dotState as $key => $value) {
            // Пропускаем служебные поля или массивы, если таковые попадут
            if (!is_string($key) || is_array($value)) {
                continue;
            }

            $pageSlug = explode('.', $key)[0] ?? 'general';

            PageContent::updateOrCreate(
                ['key' => $key],
                [
                    'page_slug' => $pageSlug,
                    'value' => (string) $value,
                ]
            );

            Cache::forget("page_text_{$key}");
        }

        Notification::make()
            ->title('Контент страниц успешно обновлен')
            ->success()
            ->send();
    }
}