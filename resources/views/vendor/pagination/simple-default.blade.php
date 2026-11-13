@if ($paginator->hasPages())
    <nav>
        <ul class="flex items-center gap-2 text-sm">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="text-text-200/40" aria-disabled="true"><span>@lang('pagination.previous')</span></li>
            @else
                <li><a class="text-text-200 hover:text-text-100" href="{{ $paginator->previousPageUrl() }}" rel="prev">@lang('pagination.previous')</a></li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li><a class="text-text-200 hover:text-text-100" href="{{ $paginator->nextPageUrl() }}" rel="next">@lang('pagination.next')</a></li>
            @else
                <li class="text-text-200/40" aria-disabled="true"><span>@lang('pagination.next')</span></li>
            @endif
        </ul>
    </nav>
@endif