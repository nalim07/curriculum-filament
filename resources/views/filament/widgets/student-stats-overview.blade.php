<x-filament::widget>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        @foreach ($this->getCards() as $card)
            <x-filament::card>
                <div class="filament-stats-card">
                    <div class="filament-stats-card-header">
                        {{ $card->getLabel() }}
                    </div>
                    <div class="filament-stats-card-body">
                        <div class="filament-stats-card-number">
                            {{ $card->getValue() }}
                        </div>
                    </div>
                </div>
            </x-filament::card>
        @endforeach
    </div>
</x-filament::widget>
