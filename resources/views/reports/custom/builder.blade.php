@extends('layouts.app')

@section('title', isset($savedReport) ? 'Edit Report' : 'Report Builder')
@section('page-title', isset($savedReport) ? 'Edit Report' : 'Report Builder')

@section('content')
@php
    $editing   = isset($savedReport);
    $cfg       = $editing ? $savedReport->config : [];
    $selSource = $cfg['source'] ?? '';
    $selCols   = $cfg['columns'] ?? [];
    $selSort   = $cfg['sort_by'] ?? '';
    $selDir    = $cfg['sort_dir'] ?? 'asc';
    $selFilters= $cfg['filters'] ?? [];
@endphp

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div>
        <a href="{{ route('reports.custom.index') }}" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i>Saved Reports
        </a>
        <h4 class="mb-1 mt-1">{{ $editing ? 'Edit: '.$savedReport->name : 'Report Builder' }}</h4>
        <p class="text-muted small mb-0">Build a custom report by choosing a data source, columns, and filters.</p>
    </div>
</div>

<div class="row g-4">
    {{-- ── LEFT: Config Panel ──────────────────────────────────────────── --}}
    <div class="col-lg-5">

        {{-- 1. Data Source --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-bottom-0 pb-0">
                <h6 class="fw-semibold mb-0"><span class="badge bg-primary me-2">1</span>Data Source</h6>
            </div>
            <div class="card-body">
                <div class="row g-2" id="sourceCards">
                    @foreach($sources as $key => $src)
                    <div class="col-6">
                        <div class="source-card card border {{ $selSource === $key ? 'border-primary bg-primary bg-opacity-10' : '' }} cursor-pointer"
                             data-source="{{ $key }}" style="cursor:pointer;" onclick="selectSource('{{ $key }}')">
                            <div class="card-body py-2 px-3 d-flex align-items-center gap-2">
                                <i class="bi {{ $src['icon'] }} fs-5 text-primary"></i>
                                <span class="small fw-semibold">{{ $src['label'] }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <input type="hidden" id="selectedSource" value="{{ $selSource }}">
            </div>
        </div>

        {{-- 2. Columns --}}
        <div class="card border-0 shadow-sm mb-3" id="colSection" style="{{ $selSource ? '' : 'display:none' }}">
            <div class="card-header bg-transparent border-bottom-0 pb-0 d-flex align-items-center justify-content-between">
                <h6 class="fw-semibold mb-0"><span class="badge bg-primary me-2">2</span>Columns</h6>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none" onclick="toggleAll(true)">All</button>
                    <span class="text-muted">/</span>
                    <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none" onclick="toggleAll(false)">None</button>
                </div>
            </div>
            <div class="card-body" id="colCheckboxes">
                @foreach($sources as $key => $src)
                <div class="source-cols" data-source="{{ $key }}" style="display:none">
                    @foreach($src['columns'] as $col => $label)
                    <div class="form-check">
                        <input class="form-check-input col-check" type="checkbox" id="col_{{ $key }}_{{ $col }}"
                               value="{{ $col }}" {{ in_array($col, $selCols) && $selSource === $key ? 'checked' : '' }}>
                        <label class="form-check-label small" for="col_{{ $key }}_{{ $col }}">{{ $label }}</label>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>

        {{-- 3. Filters --}}
        <div class="card border-0 shadow-sm mb-3" id="filterSection" style="{{ $selSource ? '' : 'display:none' }}">
            <div class="card-header bg-transparent border-bottom-0 pb-0 d-flex align-items-center justify-content-between">
                <h6 class="fw-semibold mb-0"><span class="badge bg-primary me-2">3</span>Filters</h6>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addFilter()">
                    <i class="bi bi-plus"></i> Add
                </button>
            </div>
            <div class="card-body" id="filterRows">
                {{-- populated by JS --}}
            </div>
        </div>

        {{-- 4. Sort --}}
        <div class="card border-0 shadow-sm mb-3" id="sortSection" style="{{ $selSource ? '' : 'display:none' }}">
            <div class="card-header bg-transparent border-bottom-0 pb-0">
                <h6 class="fw-semibold mb-0"><span class="badge bg-primary me-2">4</span>Sort</h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-8">
                        <select class="form-select form-select-sm" id="sortBy">
                            <option value="">No sort</option>
                            @foreach($sources as $key => $src)
                                @foreach($src['columns'] as $col => $label)
                                    <option value="{{ $col }}"
                                            data-source="{{ $key }}"
                                            {{ $selSort === $col && $selSource === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div class="col-4">
                        <select class="form-select form-select-sm" id="sortDir">
                            <option value="asc" {{ $selDir === 'asc' ? 'selected' : '' }}>Asc</option>
                            <option value="desc" {{ $selDir === 'desc' ? 'selected' : '' }}>Desc</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Preview button --}}
        <button type="button" class="btn btn-outline-primary w-100 mb-3" id="previewBtn"
                onclick="runPreview()" {{ $selSource ? '' : 'disabled' }}>
            <i class="bi bi-eye me-1"></i> Preview Results
        </button>
    </div>

    {{-- ── RIGHT: Preview + Save ─────────────────────────────────────────── --}}
    <div class="col-lg-7">

        {{-- Save card --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex gap-2 flex-wrap">
                    <input type="text" class="form-control flex-grow-1" id="reportName"
                           placeholder="Report name…"
                           value="{{ $editing ? $savedReport->name : '' }}"
                           style="max-width:320px">
                    <button type="button" class="btn btn-primary" onclick="saveReport()" id="saveBtn">
                        <i class="bi bi-floppy me-1"></i>
                        {{ $editing ? 'Update Report' : 'Save Report' }}
                    </button>
                    @if($editing)
                    <a href="{{ route('reports.custom.run', $savedReport) }}" class="btn btn-outline-success">
                        <i class="bi bi-play me-1"></i> Run
                    </a>
                    @endif
                </div>
                <div id="saveMsg" class="mt-2" style="display:none"></div>
            </div>
        </div>

        {{-- Preview results --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                <h6 class="fw-semibold mb-0">Preview</h6>
                <span id="previewCount" class="badge bg-secondary bg-opacity-10 text-secondary" style="display:none"></span>
            </div>
            <div class="card-body p-0" id="previewBody">
                <div class="text-center text-muted py-5 small" id="previewPlaceholder">
                    <i class="bi bi-table fs-1 opacity-25"></i><br>
                    Configure your report and click <strong>Preview</strong>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Hidden filter row template ──────────────────────────────────────── --}}
<template id="filterRowTpl">
    <div class="filter-row d-flex gap-2 mb-2 align-items-start">
        <select class="form-select form-select-sm filter-field" style="min-width:110px" onchange="filterFieldChanged(this)">
            <option value="">Field…</option>
        </select>
        <select class="form-select form-select-sm filter-op" style="min-width:110px">
            <option value="equals">equals</option>
            <option value="not_equals">not equals</option>
            <option value="contains">contains</option>
            <option value="starts_with">starts with</option>
            <option value="greater_than">greater than</option>
            <option value="less_than">less than</option>
            <option value="is_empty">is empty</option>
            <option value="is_not_empty">is not empty</option>
        </select>
        <div class="filter-val-wrap flex-grow-1">
            <input type="text" class="form-control form-control-sm filter-val" placeholder="Value…">
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0" onclick="removeFilter(this)">
            <i class="bi bi-x"></i>
        </button>
    </div>
</template>
@endsection

@push('scripts')
<script>
const SOURCES = @json($sources);
const EDITING = @json($editing);
const SAVED_ID = @json($editing ? $savedReport->id : null);
const SAVED_FILTERS = @json($selFilters);

const operators = {
    text:   ['equals','not_equals','contains','starts_with','is_empty','is_not_empty'],
    select: ['equals','not_equals','is_empty','is_not_empty'],
    date:   ['equals','greater_than','less_than','is_empty','is_not_empty'],
    number: ['equals','not_equals','greater_than','less_than','is_empty','is_not_empty'],
};

function selectSource(key) {
    document.getElementById('selectedSource').value = key;

    // Highlight card
    document.querySelectorAll('.source-card').forEach(c => {
        c.classList.toggle('border-primary', c.dataset.source === key);
        c.classList.toggle('bg-primary', c.dataset.source === key);
        c.classList.toggle('bg-opacity-10', c.dataset.source === key);
    });

    // Show column group for this source
    document.querySelectorAll('.source-cols').forEach(el => {
        el.style.display = el.dataset.source === key ? '' : 'none';
    });

    // Show sections
    ['colSection','filterSection','sortSection'].forEach(id => {
        document.getElementById(id).style.display = '';
    });
    document.getElementById('previewBtn').disabled = false;

    // Rebuild filter field dropdowns
    document.querySelectorAll('.filter-field').forEach(sel => rebuildFilterFields(sel, key));

    // Filter sort options to this source
    document.querySelectorAll('#sortBy option[data-source]').forEach(opt => {
        opt.style.display = opt.dataset.source === key ? '' : 'none';
    });
    const sortBy = document.getElementById('sortBy');
    if (sortBy.value && document.querySelector(`#sortBy option[value="${sortBy.value}"][data-source="${key}"]`) === null) {
        sortBy.value = '';
    }
}

function toggleAll(check) {
    const src = document.getElementById('selectedSource').value;
    document.querySelectorAll(`.source-cols[data-source="${src}"] .col-check`).forEach(cb => cb.checked = check);
}

function addFilter(field = '', op = 'equals', val = '') {
    const tpl  = document.getElementById('filterRowTpl').content.cloneNode(true);
    const row  = tpl.querySelector('.filter-row');
    const src  = document.getElementById('selectedSource').value;

    rebuildFilterFields(row.querySelector('.filter-field'), src, field);

    const opSel = row.querySelector('.filter-op');
    if (op) opSel.value = op;

    row.querySelector('.filter-val').value = val;

    if (field) filterFieldChanged(row.querySelector('.filter-field'));

    document.getElementById('filterRows').appendChild(tpl);
}

function removeFilter(btn) {
    btn.closest('.filter-row').remove();
}

function rebuildFilterFields(sel, sourceKey, selectedVal = '') {
    const src = SOURCES[sourceKey];
    sel.innerHTML = '<option value="">Field…</option>';
    if (!src) return;
    Object.entries(src.filter_fields).forEach(([fkey, fdef]) => {
        const opt = document.createElement('option');
        opt.value = fkey;
        opt.textContent = fdef.label;
        if (fkey === selectedVal) opt.selected = true;
        sel.appendChild(opt);
    });
}

function filterFieldChanged(sel) {
    const row   = sel.closest('.filter-row');
    const src   = document.getElementById('selectedSource').value;
    const field = sel.value;
    const fdef  = SOURCES[src]?.filter_fields?.[field];

    if (!fdef) return;

    // Restrict operators
    const opSel  = row.querySelector('.filter-op');
    const curOp  = opSel.value;
    const allowed = operators[fdef.type] ?? operators.text;
    Array.from(opSel.options).forEach(o => {
        o.hidden = !allowed.includes(o.value);
    });
    if (!allowed.includes(curOp)) opSel.value = allowed[0];

    // Replace value input
    const wrap = row.querySelector('.filter-val-wrap');
    wrap.innerHTML = '';
    if (fdef.type === 'select') {
        const sl = document.createElement('select');
        sl.className = 'form-select form-select-sm filter-val';
        sl.innerHTML = '<option value="">Any…</option>';
        Object.entries(fdef.options).forEach(([k, v]) => {
            const o = document.createElement('option');
            o.value = k; o.textContent = v;
            sl.appendChild(o);
        });
        wrap.appendChild(sl);
    } else if (fdef.type === 'date') {
        const inp = document.createElement('input');
        inp.type = 'date'; inp.className = 'form-control form-control-sm filter-val';
        wrap.appendChild(inp);
    } else if (fdef.type === 'number') {
        const inp = document.createElement('input');
        inp.type = 'number'; inp.className = 'form-control form-control-sm filter-val'; inp.placeholder = 'Value…';
        wrap.appendChild(inp);
    } else {
        const inp = document.createElement('input');
        inp.type = 'text'; inp.className = 'form-control form-control-sm filter-val'; inp.placeholder = 'Value…';
        wrap.appendChild(inp);
    }
}

function buildConfig() {
    const src  = document.getElementById('selectedSource').value;
    const cols = [...document.querySelectorAll(`.source-cols[data-source="${src}"] .col-check:checked`)].map(c => c.value);

    const filters = [];
    document.querySelectorAll('.filter-row').forEach(row => {
        const field = row.querySelector('.filter-field').value;
        const op    = row.querySelector('.filter-op').value;
        const val   = row.querySelector('.filter-val')?.value ?? '';
        if (field && op) filters.push({ field, operator: op, value: val });
    });

    return {
        source:   src,
        columns:  cols,
        filters:  filters,
        sort_by:  document.getElementById('sortBy').value,
        sort_dir: document.getElementById('sortDir').value,
    };
}

async function runPreview() {
    const config = buildConfig();
    if (!config.source) return alert('Please select a data source.');
    if (!config.columns.length) return alert('Please select at least one column.');

    const btn = document.getElementById('previewBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Loading…';

    try {
        const res = await fetch('{{ route('reports.custom.preview') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify(config),
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message ?? 'Preview failed');
        renderPreview(data);
    } catch (e) {
        document.getElementById('previewBody').innerHTML =
            '<div class="alert alert-danger m-3 small">' + escHtml(e.message) + '</div>';
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-eye me-1"></i> Preview Results';
    }
}

function escHtml(s) {
    if (s === null || s === undefined) return '—';
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function renderPreview(data) {
    const countEl = document.getElementById('previewCount');
    countEl.textContent = `${data.count} row${data.count !== 1 ? 's' : ''}`;
    countEl.style.display = '';

    if (!data.rows.length) {
        document.getElementById('previewBody').innerHTML =
            '<p class="text-muted text-center small py-4">No results.</p>';
        return;
    }

    let html = '<div class="table-responsive"><table class="table table-sm table-hover mb-0 small">';
    html += '<thead class="table-light"><tr>';
    data.headers.forEach(h => html += '<th>' + escHtml(h) + '</th>');
    html += '</tr></thead><tbody>';
    data.rows.forEach(row => {
        html += '<tr>';
        row.forEach(cell => html += '<td>' + escHtml(cell) + '</td>');
        html += '</tr>';
    });
    html += '</tbody></table></div>';
    document.getElementById('previewBody').innerHTML = html;
    document.getElementById('previewPlaceholder')?.remove();
}

async function saveReport() {
    const name = document.getElementById('reportName').value.trim();
    if (!name) return alert('Please enter a report name.');
    const config = buildConfig();
    if (!config.source) return alert('Please select a data source.');
    if (!config.columns.length) return alert('Please select at least one column.');

    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving…';

    const url    = EDITING ? `/reports/custom/${SAVED_ID}` : '{{ route('reports.custom.store') }}';
    const method = EDITING ? 'PUT' : 'POST';

    try {
        const res = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ name, config }),
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message ?? 'Save failed');

        const msgEl = document.getElementById('saveMsg');
        msgEl.innerHTML = '<div class="alert alert-success alert-dismissible fade show py-2 small mb-0">'
            + '<i class="bi bi-check-circle me-1"></i>' + escHtml(data.message)
            + '<button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button></div>';
        msgEl.style.display = '';

        if (!EDITING) {
            setTimeout(() => { window.location = '{{ route('reports.custom.index') }}'; }, 1200);
        }
    } catch (e) {
        alert(e.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = `<i class="bi bi-floppy me-1"></i>${EDITING ? 'Update Report' : 'Save Report'}`;
    }
}

// ── Init ─────────────────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', () => {
    const src = document.getElementById('selectedSource').value;
    if (src) {
        selectSource(src);

        // Pre-tick saved columns
        @foreach($sources as $key => $src)
        @foreach($src['columns'] as $col => $label)
        document.getElementById('col_{{ $key }}_{{ $col }}').checked =
            {{ json_encode(in_array($col, $selCols) && $selSource === $key) }};
        @endforeach
        @endforeach

        // Restore saved filters
        SAVED_FILTERS.forEach(f => addFilter(f.field, f.operator, f.value));
    }
});
</script>
@endpush
