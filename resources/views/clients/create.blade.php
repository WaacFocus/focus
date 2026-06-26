@extends('layouts.app')

@section('title', 'New Client')
@section('page-title', 'New Client')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">New Client</h4>
    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<form method="POST" action="{{ route('clients.store') }}" id="clientForm" novalidate>
    @csrf
    @include('clients._form')
    <div class="mt-3">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Create Client</button>
        <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('clientForm').addEventListener('submit', function (e) {
    if (!this.checkValidity()) {
        e.preventDefault();
        this.classList.add('was-validated');
        var first = this.querySelector(':invalid');
        if (first) { first.scrollIntoView({ behavior: 'smooth', block: 'center' }); first.focus(); }
    }
    this.classList.add('was-validated');
});
</script>
@endpush
