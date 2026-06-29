@extends('layouts.app')

@section('title', 'Edit Client')
@section('page-title', 'Edit Client')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit: {{ $client->company_name }}</h4>
    <div class="d-flex gap-2">
        @if(!$client->ch_status && ($client->company_number || preg_match('/\b(limited|ltd|plc|llp)\b/i', $client->clientType?->name ?? '')))
        <a href="{{ route('clients.show', $client) }}?sync=1" class="btn btn-outline-primary">
            <img src="{{ asset('images/ch-icon.svg') }}" alt="" width="14" height="14" class="me-1 align-middle">Sync with Companies House
        </a>
        @endif
        <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

<form method="POST" action="{{ route('clients.update', $client) }}" id="clientForm" novalidate autocomplete="off">
    @csrf @method('PUT')
    @include('clients._form')
    <div class="mt-3">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
        <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-secondary ms-2">Cancel</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
    var form = document.getElementById('clientForm');
    form.querySelectorAll('input, select, textarea').forEach(function (el, i) {
        el.setAttribute('autocomplete', 'focus-off-' + i);
    });
    form.addEventListener('submit', function (e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            this.classList.add('was-validated');
            var first = this.querySelector(':invalid');
            if (first) { first.scrollIntoView({ behavior: 'smooth', block: 'center' }); first.focus(); }
        }
        this.classList.add('was-validated');
    });
})();
</script>
@endpush
