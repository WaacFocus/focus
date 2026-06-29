@extends('layouts.app')

@section('title', $template->exists ? 'Edit Section' : 'New Section')
@section('page-title', 'Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.engagement-letter-templates.index') }}" class="text-muted text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>Letter Sections
        </a>
        <h4 class="mb-0 mt-1">{{ $template->exists ? 'Edit: '.$template->title : 'New Section' }}</h4>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger py-2 small mb-3">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
@endif

<form method="POST"
      action="{{ $template->exists
          ? route('admin.engagement-letter-templates.update', $template)
          : route('admin.engagement-letter-templates.store') }}">
    @csrf
    @if($template->exists) @method('PUT') @endif

    <div class="card shadow-sm">
        <div class="card-body row g-3">

            <div class="col-md-6">
                <label class="form-label fw-semibold">Section Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title', $template->title) }}"
                       required placeholder="e.g. Annual Accounts">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Category</label>
                <input type="text" name="service_type" class="form-control"
                       value="{{ old('service_type', $template->service_type) }}"
                       list="categoryList" placeholder="e.g. accounts, tax, payroll">
                <datalist id="categoryList">
                    <option value="general">
                    <option value="accounts">
                    <option value="tax">
                    <option value="vat">
                    <option value="payroll">
                    <option value="bookkeeping">
                    <option value="secretarial">
                </datalist>
                <div class="form-text">Used as a label in the builder — no functional effect.</div>
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Sort Order</label>
                <input type="number" name="sort_order" class="form-control"
                       value="{{ old('sort_order', $template->sort_order ?? '') }}"
                       min="0" placeholder="0">
                <div class="form-text">Lower = earlier in list.</div>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Default Wording <span class="text-danger">*</span></label>
                <textarea name="body" rows="10"
                          class="form-control @error('body') is-invalid @enderror"
                          required
                          placeholder="Enter the default text for this section…">{{ old('body', $template->body) }}</textarea>
                <div class="form-text">This is the starting content shown in the builder. Users can edit it per letter.</div>
                @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                           @checked(old('is_active', $template->is_active ?? true))>
                    <label class="form-check-label" for="isActive">Active</label>
                    <div class="form-text">Show in the letter builder.</div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="default_included" id="defaultIncluded" value="1"
                           @checked(old('default_included', $template->default_included ?? false))>
                    <label class="form-check-label" for="defaultIncluded">Pre-ticked</label>
                    <div class="form-text">Automatically selected on new letters.</div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_mandatory" id="isMandatory" value="1"
                           @checked(old('is_mandatory', $template->is_mandatory ?? false))>
                    <label class="form-check-label" for="isMandatory">Mandatory</label>
                    <div class="form-text">Cannot be removed from any new letter.</div>
                </div>
            </div>

        </div>
    </div>

    <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i>{{ $template->exists ? 'Save Changes' : 'Add Section' }}
        </button>
        <a href="{{ route('admin.engagement-letter-templates.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection
