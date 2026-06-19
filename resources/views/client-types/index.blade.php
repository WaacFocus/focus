@extends('layouts.app')

@section('title', 'Client Types')
@section('page-title', 'Admin — Client Types')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Client Types</h4>
        <small class="text-muted">Define the types of client used when adding or editing clients</small>
    </div>
    <button class="btn btn-primary" type="button" onclick="openTypePanel()">
        <i class="bi bi-plus-lg me-1"></i>New Type
    </button>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:40px;" class="text-center text-muted">#</th>
                    <th>Name</th>
                    <th class="text-center">Clients</th>
                    <th class="text-center">Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="typesTableBody">
                @forelse($clientTypes as $type)
                <tr>
                    <td class="text-center text-muted small">{{ $type->sort_order }}</td>
                    <td class="fw-semibold">{{ $type->name }}</td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark">{{ $type->clients_count }}</span>
                    </td>
                    <td class="text-center">
                        @if($type->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                onclick="openTypePanel({{ $type->id }})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                                onclick="deleteType({{ $type->id }}, '{{ addslashes($type->name) }}')"
                                title="Delete" {{ $type->clients_count > 0 ? 'disabled' : '' }}>
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No client types defined yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@include('client-types._panel')
@endsection

@push('scripts')
<script>
async function deleteType(id, name) {
    if (!confirm(`Delete "${name}"? This cannot be undone.`)) return;

    const res = await fetch(`/client-types/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: (() => { const f = new FormData(); f.append('_method', 'DELETE'); return f; })(),
    });

    const data = await res.json();

    if (res.ok) {
        window.location.reload();
    } else {
        alert(data.message ?? 'Could not delete this type.');
    }
}
</script>
@endpush
