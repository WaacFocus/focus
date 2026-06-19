@extends('layouts.app')

@section('title', 'Edit Task')
@section('page-title', 'Edit Task')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit: {{ $task->name }}</h4>
    <a href="{{ route('projects.show', $task->project) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back to Project</a>
</div>

<form method="POST" action="{{ route('tasks.update', $task) }}">
    @csrf @method('PUT')
    @include('tasks._form')
    <div class="mt-3">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
        <a href="{{ route('projects.show', $task->project) }}" class="btn btn-outline-secondary ms-2">Cancel</a>
    </div>
</form>
@endsection
