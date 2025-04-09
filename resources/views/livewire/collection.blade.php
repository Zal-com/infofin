<div class="mt-4">
    <x-filament::page>
        <h2 class="text-xl font-bold mb-4">Projets</h2>

        <x-filament::table :query="$projectsQuery">
            <x-filament::table.heading>Nom</x-filament::table.heading>
            <x-filament::table.heading>Organisation</x-filament::table.heading>
            <x-filament::table.heading>Dernière mise à jour</x-filament::table.heading>

            @foreach ($projectsQuery->get() as $record)
                <x-filament::table.row>
                    <x-filament::table.cell>{{ $record->title }}</x-filament::table.cell>
                    <x-filament::table.cell>{{ $record->organisation->title ?? '-' }}</x-filament::table.cell>
                    <x-filament::table.cell>{{ $record->updated_at->format('d/m/Y') }}</x-filament::table.cell>
                </x-filament::table.row>
            @endforeach
        </x-filament::table>

        <h2 class="text-xl font-bold mt-10 mb-4">Séances d'information</h2>

        <x-filament::table :query="$infoSessionsQuery">
            <x-filament::table.heading>Nom</x-filament::table.heading>
            <x-filament::table.heading>Organisation</x-filament::table.heading>
            <x-filament::table.heading>Dernière mise à jour</x-filament::table.heading>

            @foreach ($infoSessionsQuery->get() as $record)
                <x-filament::table.row>
                    <x-filament::table.cell>{{ $record->title }}</x-filament::table.cell>
                    <x-filament::table.cell>{{ $record->organisation->title ?? '-' }}</x-filament::table.cell>
                    <x-filament::table.cell>{{ $record->updated_at->format('d/m/Y') }}</x-filament::table.cell>
                </x-filament::table.row>
            @endforeach
        </x-filament::table>
    </x-filament::page>
</div>
