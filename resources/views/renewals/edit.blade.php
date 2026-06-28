@extends('layouts.app')

@section('title', 'Edit Engagement Letter')
@section('page-title', 'Engagement Letters')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit: {{ $renewal->description }}</h4>
    <a href="{{ route('renewals.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<form method="POST" action="{{ route('renewals.update', $renewal) }}">
    @csrf @method('PUT')
    @include('renewals._form')
    <div class="mt-3 d-flex gap-2 align-items-center flex-wrap">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
        <a href="{{ route('engagement-letters.create', ['renewal_id' => $renewal->id]) }}" class="btn btn-outline-secondary">
            <i class="bi bi-envelope-open me-1"></i>Build Engagement Letter
        </a>
        <a href="{{ route('renewals.index') }}" class="btn btn-link text-muted ms-auto">Cancel</a>
    </div>
</form>
@endsection
