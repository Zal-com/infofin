@if ($paginator->hasPages())
    <nav aria-label="Pagination Navigation">
        <ul class="inline-flex -space-x-px text-sm">

            @if ($paginator->onFirstPage())
                <li>
                    <span class="flex items-center justify-center px-3 h-8 text-gray-500 bg-white border border-gray-300 rounded-s-lg cursor-default">Previous</span>
                </li>
            @else
                <li>
                    <button wire:click="previousPage" class="flex items-center justify-center px-3 h-8 text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700">Previous</button>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li>
                        <span class="flex items-center justify-center px-3 h-8 text-gray-500 bg-white border border-gray-300 cursor-default">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span class="flex items-center justify-center px-3 h-8 text-blue-600 bg-blue-50 border border-gray-300 cursor-default">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <button wire:click="gotoPage({{ $page }})" class="flex items-center justify-center px-3 h-8 text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $page }}</button>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li>
                    <button wire:click="nextPage" class="flex items-center justify-center px-3 h-8 text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700">Next</button>
                </li>
            @else
                <li>
                    <span class="flex items-center justify-center px-3 h-8 text-gray-500 bg-white border border-gray-300 rounded-e-lg cursor-default">Next</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
