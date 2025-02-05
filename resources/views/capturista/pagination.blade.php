<ul class="pagination pagination-sm" id="paginationLinks">
    <li class="page-item {{ $alumnos->onFirstPage() ? 'disabled' : '' }}">
        <a class="page-link" href="{{ $alumnos->previousPageUrl() }}">
            <i class="fas fa-chevron-left fa-sm"></i>
        </a>
    </li>

    @foreach ($alumnos->getUrlRange(1, $alumnos->lastPage()) as $page => $url)
        <li class="page-item {{ $alumnos->currentPage() == $page ? 'active' : '' }}">
            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
        </li>
    @endforeach

    <li class="page-item {{ $alumnos->hasMorePages() ? '' : 'disabled' }}">
        <a class="page-link" href="{{ $alumnos->nextPageUrl() }}">
            <i class="fas fa-chevron-right fa-sm"></i>
        </a>
    </li>
</ul>
