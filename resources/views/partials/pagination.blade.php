@if($paginator->total() > 0)
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 px-1 py-2">

    {{-- Count + per-page selector --}}
    <div class="d-flex align-items-center gap-3">
        <span class="text-muted small">
            @if($paginator->total() > 0)
                Showing {{ number_format($paginator->firstItem()) }}–{{ number_format($paginator->lastItem()) }}
                of {{ number_format($paginator->total()) }}
            @else
                No results
            @endif
        </span>

        <form method="GET" class="d-inline-flex align-items-center gap-1 mb-0">
            @foreach(request()->except('per_page', 'page') as $key => $val)
                @if(is_array($val))
                    @foreach($val as $v)
                        <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endif
            @endforeach
            <select name="per_page" class="form-select form-select-sm text-muted"
                    style="width:auto;font-size:.8rem;"
                    onchange="this.form.submit()">
                @foreach([25, 50, 100, 250] as $size)
                    <option value="{{ $size }}" @selected((int) request('per_page', 25) === $size)>
                        {{ $size }} per page
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Page navigation --}}
    @if($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last    = $paginator->lastPage();
        // Build page list with ellipsis markers (null)
        $pages = [];
        for ($i = 1; $i <= $last; $i++) {
            if ($i === 1 || $i === $last || abs($i - $current) <= 2) {
                $pages[] = $i;
            }
        }
        // Insert nulls for gaps
        $withGaps = [];
        $prev = null;
        foreach ($pages as $p) {
            if ($prev !== null && $p - $prev > 1) {
                $withGaps[] = null;
            }
            $withGaps[] = $p;
            $prev = $p;
        }
    @endphp
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm mb-0">
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $paginator->previousPageUrl() ?? '#' }}" aria-label="Previous">
                    <i class="bi bi-chevron-left" style="font-size:.7rem;"></i>
                </a>
            </li>
            @foreach($withGaps as $p)
                @if($p === null)
                    <li class="page-item disabled"><span class="page-link px-2">…</span></li>
                @else
                    <li class="page-item {{ $p === $current ? 'active' : '' }}">
                        <a class="page-link" href="{{ $paginator->url($p) }}">{{ $p }}</a>
                    </li>
                @endif
            @endforeach
            <li class="page-item {{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $paginator->nextPageUrl() ?? '#' }}" aria-label="Next">
                    <i class="bi bi-chevron-right" style="font-size:.7rem;"></i>
                </a>
            </li>
        </ul>
    </nav>
    @endif

</div>
@endif
