@extends('layouts.app')

@section('title', 'New Renewal')
@section('page-title', 'New Renewal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">New Renewal</h4>
    <a href="{{ route('renewals.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<form method="POST" action="{{ route('renewals.store') }}">
    @csrf
    @include('renewals._form')
    <div class="mt-3">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Create Renewal</button>
        <a href="{{ route('renewals.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
    </div>
</form>
@endsection
