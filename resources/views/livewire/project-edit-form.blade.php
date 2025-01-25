<div>
    <div wire:ignore.self>
        @if (session('success'))
            <div class="mt-4 p-4 bg-green-500 text-white">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mt-4 p-4 bg-red-500 text-white rounded-2xl mb-4">
                @foreach (session('error') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </div>
        @endif

        <!-- Modal -->
        <div x-data="{ open: @entangle('showModal') }">
            <div
                x-show="open"
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
                style="display: none;"
            >
                <div class="bg-white rounded-lg shadow-lg p-6 max-w-lg w-full">
                    <h2 class="text-lg font-semibold mb-4">Inclure dans la prochaine newsletter ?</h2>
                    <p class="mb-6">Souhaitez-vous que cette publication soit ajoutée à la prochaine newsletter ?</p>

                    <div class="flex justify-end space-x-4">
                        <button
                            @click="open = false; @this.set('isInNextEmail', false); @this.submit();"
                            class="bg-gray-500 text-white px-4 py-2 rounded"
                        >
                            Non
                        </button>
                        <button
                            @click="open = false; @this.set('isInNextEmail', true); @this.submit();"
                            class="bg-blue-500 text-white px-4 py-2 rounded"
                        >
                            Oui
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fin du modal -->

        <form wire:submit.prevent="submit" wire:model="project">
            {{ $this->form }}
        </form>
        <x-filament-actions::modals/>
    </div>
</div>
