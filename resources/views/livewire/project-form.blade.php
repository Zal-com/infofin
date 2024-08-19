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
        <form wire:submit.prevent="submit" wire:model="project">
            @csrf
            {{ $this->form }}
            <x-filament-actions::modals/>
        </form>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const trixEditor = document.querySelector('trix-editor');
                const maxChars = 500;

                trixEditor.addEventListener('trix-change', function () {
                    let content = trixEditor.innerText;
                    const editor = trixEditor.editor;

                    if (content.length > maxChars) {
                        const truncatedContent = content.substring(0, maxChars);
                        editor.setSelectedRange([0, content.length]);
                        editor.insertString(truncatedContent);
                    }
                });
            });
        </script>
    </div>
</div>
