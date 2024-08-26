<div>
    <div class="flex flex-row gap-4">
        <!-- Table Section -->
        <div class="w-full">
            {{ $this->table }}
        </div>

        <!-- Information Box -->
        <x-filament::section style="min-width: 200px; height: fit-content">
            <x-filament::section.heading class="text-lg font-semibold mb-2">Prochain mail
            </x-filament::section.heading>
            <div class="flex gap-2">
                <x-filament::icon icon="heroicon-o-calendar" style="max-height: 24px; max-width: 24px;"/>
                {{$this->schedule->day_of_week}}

            </div>
            <div class="flex gap-2">
                <x-filament::icon icon="heroicon-o-clock" style="max-height: 24px; max-width: 24px;"/>
                {{$this->schedule->send_time}}
            </div>

        </x-filament::section>

    </div>
    <div class="w-full" style="margin-top: 30px">
        {{ $this->form }}

    </div>

</div>
