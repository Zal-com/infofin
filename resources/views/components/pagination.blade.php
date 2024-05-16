@if ($paginator->hasPages())
    <nav aria-label="Pagination Navigation">
        <ul class="inline-flex -space-x-px text-sm">

            @if ($paginator->onFirstPage())
                <li>
                    <span class="flex items-center justify-center px-3 h-8 text-gray-500 bg-white border border-gray-300 rounded-s-lg cursor-default">Previous</span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" class="flex items-center justify-center px-3 h-8 text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700">Previous</a>
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
                        @elseif ($page == 1 || $page == $paginator->lastPage() || 
                                 ($page >= $paginator->currentPage() - 1 && $page <= $paginator->currentPage() + 1))
                            <li>
                                <a href="{{ $url }}" class="flex items-center justify-center px-3 h-8 text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $page }}</a>
                            </li>
                        @elseif ($page == 2 && $paginator->currentPage() > 4)
                            <li>
                                <span class="flex items-center justify-center px-3 h-8 text-gray-500 bg-white border border-gray-300 cursor-default">...</span>
                            </li>
                        @elseif ($page == $paginator->lastPage() - 1 && $paginator->currentPage() < $paginator->lastPage() - 3)
                            <li>
                                <span class="flex items-center justify-center px-3 h-8 text-gray-500 bg-white border border-gray-300 cursor-default">...</span>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" class="flex items-center justify-center px-3 h-8 text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700">Next</a>
                </li>
            @else
                <li>
                    <span class="flex items-center justify-center px-3 h-8 text-gray-500 bg-white border border-gray-300 rounded-e-lg cursor-default">Next</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
