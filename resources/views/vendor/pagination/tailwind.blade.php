<div class="columns is-flex is-justify-content-start" style="margin-left: 10px; margin-top: 25px; margin-bottom: 10px">
@if ($paginator->hasPages())
    <nav class="pagination is-rounded is-3" role="navigation" aria-label="pagination">
    @if (!$paginator->onFirstPage())
            <a href="{{ $paginator->previousPageUrl() }}" aria-label="@lang('pagination.next')" class="pagination-previous"><ion-icon name="chevron-back-outline"></ion-icon> Voltar</a>
        @endif

    @foreach ($elements as $element)

        {{-- Array Of Links --}}
        @if (is_array($element))
            <ul class="pagination-list">
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                        <li><a class="pagination-link is-current" aria-label="Página {{ $page }}">{{ $page }}</a></li>
                @else
                        <li><a href="{{ $url }}" class="pagination-link" aria-label="Ir à página {{ $page }}">{{ $page }}</a></li>
                    @endif
            @endforeach
            </ul>
        @endif
    @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" aria-label="@lang('pagination.next')" class="pagination-previous"><ion-icon name="chevron-forward-outline"></ion-icon> Próximo</a>
        @endif
    </nav>
@endif
</div>
