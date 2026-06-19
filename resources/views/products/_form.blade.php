<div class="card shadow-sm">
    <div class="card-body row g-3">
        <div class="col-md-8">
            <label class="form-label">Product Name <span class="text-danger">*</span></label>
            <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label">SKU</label>
            <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Category</label>
            <input type="text" name="category" value="{{ old('category', $product->category ?? '') }}" class="form-control" placeholder="e.g. Accountancy, Software">
        </div>
        <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" rows="2" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Unit Price (£) <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">£</span>
                <input type="number" name="unit_price" value="{{ old('unit_price', $product->unit_price ?? '0') }}" class="form-control @error('unit_price') is-invalid @enderror" step="0.01" min="0" required>
                @error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Unit <span class="text-danger">*</span></label>
            <input type="text" name="unit" value="{{ old('unit', $product->unit ?? 'hour') }}" class="form-control" placeholder="e.g. hour, day, item" required>
        </div>
    </div>
</div>
