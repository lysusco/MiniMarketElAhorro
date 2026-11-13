<!-- bg-bg-100 bg-bg-200 bg-bg-300 text-text-100 text-text-200 border-bg-300 bg-primary-100 bg-primary-200 bg-primary-300 text-primary-300 bg-accent-100 bg-accent-200 text-accent-200 -->
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex gap-2 items-center justify-between">

        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-text-200 bg-bg-200 border border-bg-300 cursor-not-allowed leading-5 rounded-md">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-4 py-2 text-sm font-medium text-text-100 bg-bg-200 border border-bg-300 leading-5 rounded-md hover:text-text-200 focus:outline-none focus:ring ring-bg-300 focus:border-primary-300 active:bg-bg-300 active:text-text-100 transition ease-in-out duration-150 hover:bg-bg-300 hover:text-text-100">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-4 py-2 text-sm font-medium text-text-100 bg-bg-200 border border-bg-300 leading-5 rounded-md hover:text-text-200 focus:outline-none focus:ring ring-bg-300 focus:border-primary-300 active:bg-bg-300 active:text-text-100 transition ease-in-out duration-150 hover:bg-bg-300 hover:text-text-100">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-text-200 bg-bg-200 border border-bg-300 cursor-not-allowed leading-5 rounded-md">
                {!! __('pagination.next') !!}
            </span>
        @endif

    </nav>
@endif