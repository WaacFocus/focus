@extends('layouts.app')

@section('title', 'Edit Service')
@section('page-title', 'Edit Service')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit: {{ $service->name }}</h4>
    <a href="{{ route('services.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<form method="POST" action="{{ route('services.update', $service) }}">
    @csrf @method('PUT')
    @include('services._form')
    <div class="mt-3">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
        <a href="{{ route('services.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
    </div>
</form>
@endsection
