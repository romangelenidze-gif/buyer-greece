<x-layouts.public 
    title="{{ __('privacy.title') }} | Exereto"
>
<div class="min-h-[calc(100vh-4rem)] bg-[#f5f5f7] text-[#1d1d1f] py-12 px-6 sm:px-8 lg:px-8">
    <div class="max-w-4xl mx-auto apple-card space-y-8">
        
        <div class="border-b border-black/[0.06] pb-6 space-y-2">
            <h1 class="apple-section-title">
                {{ __('privacy.title') }}
            </h1>
            <p class="app-text-sub">{{ __('privacy.updated') }}</p>
        </div>

        <div class="space-y-6">
            @foreach(__('privacy.items') as $item)
                <section class="space-y-3">
                    @if(!empty($item['title']))
                        <h2 class="app-text-title text-xl">{{ $item['title'] }}</h2>
                    @endif
                    <p class="app-text-body">{{ $item['text'] }}</p>
                </section>
            @endforeach
        </div>

    </div>
</div>
</x-layouts.public>