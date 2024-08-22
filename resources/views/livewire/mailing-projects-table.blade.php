@php use Carbon\Carbon; @endphp
<div class="flex flex-row gap-4">
    <!-- Table Section -->
    <div class="w-full">
        {{ $this->table }}
    </div>

    <!-- Information Box -->
    <div style="min-width: 200px; height: fit-content"
         class="justify-end p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
        <h3 class="text-lg font-semibold mb-2">Prochain mail</h3>
        <div class="flex gap-2">
            <x-filament::icon icon="heroicon-o-calendar" style="max-height: 24px; max-width: 24px;"/>
            {{$this->schedule->day_of_week}}

        </div>
        <div class="flex gap-2">
            <x-filament::icon icon="heroicon-o-clock" style="max-height: 24px; max-width: 24px;"/>
            {{$this->schedule->send_time}}
        </div>

    </div>
</div>
