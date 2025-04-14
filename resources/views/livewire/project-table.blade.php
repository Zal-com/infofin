<div class="mt-4">
    @if ($this->getTableRecords()->isNotEmpty())
        <h2 class="text-2xl font-semibold">Appels</h2>
        {{ $this->table }}
    @endif
</div>
