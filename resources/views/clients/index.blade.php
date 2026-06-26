@extends('layouts.app')

@section('title', 'Clients')
@section('page-title', 'Clients')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Clients</h4>
    <div class="d-flex gap-2">
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="colToggleBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                <i class="bi bi-layout-three-columns me-1"></i>Columns
            </button>
            <ul class="dropdown-menu dropdown-menu-end p-2" style="min-width:160px;" id="colToggleMenu">
                <li><label class="dropdown-item rounded d-flex align-items-center gap-2 py-1 px-2"><input type="checkbox" class="col-toggle-check" data-col="col-code" checked> Code</label></li>
                <li><label class="dropdown-item rounded d-flex align-items-center gap-2 py-1 px-2"><input type="checkbox" class="col-toggle-check" data-col="col-type" checked> Type</label></li>
                <li><label class="dropdown-item rounded d-flex align-items-center gap-2 py-1 px-2"><input type="checkbox" class="col-toggle-check" data-col="col-contact" checked> Contact</label></li>
                <li><label class="dropdown-item rounded d-flex align-items-center gap-2 py-1 px-2"><input type="checkbox" class="col-toggle-check" data-col="col-email" checked> Email</label></li>
                <li><label class="dropdown-item rounded d-flex align-items-center gap-2 py-1 px-2"><input type="checkbox" class="col-toggle-check" data-col="col-phone" checked> Phone</label></li>
                <li><label class="dropdown-item rounded d-flex align-items-center gap-2 py-1 px-2"><input type="checkbox" class="col-toggle-check" data-col="col-status" checked> Status</label></li>
            </ul>
        </div>
        <button class="btn btn-primary" type="button" onclick="openClientPanel()">
            <i class="bi bi-plus-lg me-1"></i>New Client
        </button>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-6">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name, contact or email...">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active"   @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    <option value="prospect" @selected(request('status') === 'prospect')>Prospect</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-outline-secondary flex-grow-1"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle" id="clientsTable">
            <thead class="table-light">
                <tr>
                    <th class="col-code">Code</th>
                    <th>Company</th>
                    <th class="col-type">Type</th>
                    <th class="col-contact">Contact</th>
                    <th class="col-email">Email</th>
                    <th class="col-phone">Phone</th>
                    <th class="col-status text-center">Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr>
                    <td class="col-code"><span class="text-muted small fw-semibold">{{ $client->client_code ?? '—' }}</span></td>
                    <td><a href="{{ route('clients.show', $client) }}" class="fw-semibold text-decoration-none">{{ $client->company_name }}</a></td>
                    <td class="col-type"><span class="text-muted small">{{ $client->clientType?->name ?? '—' }}</span></td>
                    <td class="col-contact">{{ $client->contact_name }}</td>
                    <td class="col-email"><a href="mailto:{{ $client->email }}" class="text-decoration-none">{{ $client->email }}</a></td>
                    <td class="col-phone">{{ $client->phone }}</td>
                    <td class="col-status text-center"><span class="badge bg-{{ $client->status_badge }}">{{ ucfirst($client->status) }}</span></td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="openClientPanel({{ $client->id }})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @can('manager')
                        <form method="POST" action="{{ route('clients.destroy', $client) }}" class="d-inline" onsubmit="return confirm('Delete this client?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No clients found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($clients->hasPages())
        <div class="card-footer bg-white">{{ $clients->links() }}</div>
    @endif
</div>

@include('clients._panel')
@endsection

@push('styles')
<style>
.col-toggle-check {
    width: 1em; height: 1em; margin-top: 0;
    appearance: none; -webkit-appearance: none;
    border: 1.5px solid #adb5bd; border-radius: .2em;
    background: #fff; cursor: pointer; flex-shrink: 0;
    transition: background .12s, border-color .12s;
}
.col-toggle-check:checked {
    background-color: var(--brand-dark);
    border-color: var(--brand-dark);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-width='3' d='m6 10 3 3 5-5'/%3e%3c/svg%3e");
    background-repeat: no-repeat; background-size: cover;
}
.col-toggle-check:focus { outline: none; box-shadow: 0 0 0 .2rem rgba(12,61,56,.25); }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const STORAGE_KEY = 'clients_col_prefs';
    const checkboxes  = document.querySelectorAll('.col-toggle-check');

    function applyVisibility(col, visible) {
        document.querySelectorAll('#clientsTable .' + col).forEach(function (el) {
            el.style.display = visible ? '' : 'none';
        });
    }

    function savePrefs() {
        const prefs = {};
        checkboxes.forEach(function (cb) { prefs[cb.dataset.col] = cb.checked; });
        localStorage.setItem(STORAGE_KEY, JSON.stringify(prefs));
    }

    function loadPrefs() {
        try {
            const saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
            checkboxes.forEach(function (cb) {
                if (cb.dataset.col in saved) {
                    cb.checked = saved[cb.dataset.col];
                }
                applyVisibility(cb.dataset.col, cb.checked);
            });
        } catch (e) {}
    }

    checkboxes.forEach(function (cb) {
        cb.addEventListener('change', function () {
            applyVisibility(cb.dataset.col, cb.checked);
            savePrefs();
        });
    });

    loadPrefs();
})();
</script>
@endpush
