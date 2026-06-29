@extends('layouts.app')

@section('title', 'Engagement Letter Sections')
@section('page-title', 'Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Engagement Letter Sections</h4>
        <p class="text-muted small mb-0">Drag rows to reorder. Changes apply to new letters only.</p>
    </div>
    <a href="{{ route('admin.engagement-letter-templates.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add Section
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success py-2 small mb-3">{{ session('success') }}</div>
@endif

<div id="saveOrderAlert" class="alert alert-info py-2 small mb-3 d-none">
    <i class="bi bi-arrow-down-up me-1"></i>Order changed —
    <button id="saveOrderBtn" class="btn btn-sm btn-primary ms-1 py-0">Save Order</button>
    <button id="cancelOrderBtn" class="btn btn-sm btn-outline-secondary ms-1 py-0">Undo</button>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:2rem;"></th>
                    <th>Section Title</th>
                    <th>Category</th>
                    <th class="text-center">Active</th>
                    <th class="text-center">Pre-ticked</th>
                    <th class="text-center">Mandatory</th>
                    <th style="width:14rem;">Wording (preview)</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="sortableBody">
                @forelse($templates as $tpl)
                <tr data-id="{{ $tpl->id }}">
                    <td class="text-center text-muted" style="cursor:grab;">
                        <i class="bi bi-grip-vertical"></i>
                    </td>
                    <td class="fw-semibold">{{ $tpl->title }}</td>
                    <td>
                        @if($tpl->service_type)
                            <span class="badge bg-light text-dark">{{ ucfirst($tpl->service_type) }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($tpl->is_active)
                            <i class="bi bi-check-circle-fill text-success"></i>
                        @else
                            <i class="bi bi-dash-circle text-muted"></i>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($tpl->default_included)
                            <i class="bi bi-check-circle-fill text-primary" title="Pre-ticked in builder"></i>
                        @else
                            <i class="bi bi-dash-circle text-muted"></i>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($tpl->is_mandatory)
                            <i class="bi bi-lock-fill text-warning" title="Mandatory — cannot be removed"></i>
                        @else
                            <i class="bi bi-dash-circle text-muted"></i>
                        @endif
                    </td>
                    <td class="text-muted small" style="max-width:14rem;">
                        <span style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            {{ $tpl->body }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.engagement-letter-templates.edit', $tpl) }}"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.engagement-letter-templates.destroy', $tpl) }}"
                              class="d-inline"
                              onsubmit="return confirm('Delete the &quot;{{ addslashes($tpl->title) }}&quot; section?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No sections yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js"></script>
<script>
const tbody = document.getElementById('sortableBody');
const alert = document.getElementById('saveOrderAlert');
let originalOrder = [...tbody.querySelectorAll('tr[data-id]')].map(r => r.dataset.id);

const sortable = new Sortable(tbody, {
    handle: 'td:first-child',
    animation: 150,
    onEnd() {
        const current = [...tbody.querySelectorAll('tr[data-id]')].map(r => r.dataset.id);
        const changed = current.some((id, i) => id !== originalOrder[i]);
        alert.classList.toggle('d-none', !changed);
    }
});

document.getElementById('saveOrderBtn').addEventListener('click', async () => {
    const order = [...tbody.querySelectorAll('tr[data-id]')].map(r => r.dataset.id);
    const res = await fetch('{{ route('admin.engagement-letter-templates.reorder') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ order })
    });
    if (res.ok) {
        originalOrder = order;
        alert.classList.add('d-none');
    }
});

document.getElementById('cancelOrderBtn').addEventListener('click', () => {
    // Restore DOM to original order
    originalOrder.forEach(id => {
        const row = tbody.querySelector(`tr[data-id="${id}"]`);
        if (row) tbody.appendChild(row);
    });
    alert.classList.add('d-none');
});
</script>
@endpush
