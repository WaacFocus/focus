<div class="card shadow-sm">
    <div class="card-body row g-3">
        <div class="col-md-8">
            <label class="form-label">Service Name <span class="text-danger">*</span></label>
            <input type="text" name="name" value="{{ old('name', $service->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $service->is_active ?? true))>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
        </div>
        <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" rows="3" class="form-control">{{ old('description', $service->description ?? '') }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Default Price (£) <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">£</span>
                <input type="number" name="default_price" value="{{ old('default_price', $service->default_price ?? '0') }}" class="form-control @error('default_price') is-invalid @enderror" step="0.01" min="0" required>
                @error('default_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Billing Cycle</label>
            <select name="billing_cycle" class="form-select">
                @foreach(['monthly' => 'Monthly','quarterly' => 'Quarterly','annually' => 'Annually','one_off' => 'One-off'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('billing_cycle', $service->billing_cycle ?? 'annually') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
