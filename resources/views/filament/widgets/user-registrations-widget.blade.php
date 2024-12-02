<x-filament::card>
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-sm font-medium text-gray-500">Inscriptions ce mois-ci</h3>
            <p class="text-3xl font-bold">{{ $currentMonthRegistrations }}</p>
            <p class="text-xs text-gray-500">@choice('nouvel utilisateur|nouveaux utilisateurs', $currentMonthRegistrations)</p>
        </div>
        <div class="flex items-center">
            @if($isPositiveTrend)
                <x-heroicon-o-arrow-trending-up class="w-6 h-6 text-green-500"/>
            @else
                <x-heroicon-o-arrow-trending-down class="w-6 h-6 text-red-500"/>
            @endif
            <span class="ml-2 text-sm {{ $isPositiveTrend ? 'text-green-600' : 'text-red-600' }}">
                {{ abs($percentageDifference) }}%
            </span>
        </div>
    </div>
</x-filament::card>
