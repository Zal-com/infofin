<div>
    @if (session('error'))
        <div class="mt-4 p-4 bg-red-500 text-white rounded-2xl mb-4">
            @foreach (session('error') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </div>
    @endif
    @if(session('success'))
        <div class="mt-4 p-4 bg-green-500 text-white">
            {{ session('success') }}
        </div>
    @endif
    <form wire:submit.prevent="submit" wire:model="user">
        {{$this->form}}
        <div class="flex flex-row justify-end mt-6">
            <x-filament::button type="submit" style="padding: 10px !important">
                <i class="fa fa-solid fa-edit mr-2"></i>
                Modifier le profil
            </x-filament::button>
        </div>
    </form>
</div>
