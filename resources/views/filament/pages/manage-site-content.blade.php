<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex justify-end pt-4">
            <x-filament::button type="submit" size="lg">
                Сохранить изменения
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>