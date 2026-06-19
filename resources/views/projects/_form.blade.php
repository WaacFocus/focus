<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Project Details</div>
            <div class="card-body row g-3">
                <div class="col-md-8">
                    <label class="form-label">Project Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $project->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach(['quote' => 'Quote','active' => 'Active','on_hold' => 'On Hold','completed' => 'Completed','cancelled' => 'Cancelled'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('status', $project->status ?? 'active') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Client <span class="text-danger">*</span></label>
                    <select name="client_id" class="form-select @error('client_id') is-invalid @enderror" required>
                        <option value="">— Select Client —</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" @selected(old('client_id', $project->client_id ?? request('client_id')) == $c->id)>{{ $c->company_name }}</option>
                        @endforeach
                    </select>
                    @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description', $project->description ?? '') }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date', isset($project->start_date) ? $project->start_date->format('Y-m-d') : '') }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date', isset($project->end_date) ? $project->end_date->format('Y-m-d') : '') }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Budget (£)</label>
                    <input type="number" name="budget" value="{{ old('budget', $project->budget ?? '') }}" class="form-control" step="0.01" min="0">
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="3" class="form-control">{{ old('notes', $project->notes ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Cost Lines (Products)</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0" id="cost-table">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th style="width:70px">Qty</th>
                            <th style="width:90px">Price £</th>
                            <th style="width:30px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $existingProducts = isset($project) ? $project->products->keyBy('id') : collect(); @endphp
                        @foreach($products as $product)
                        @php $pivot = $existingProducts->get($product->id); @endphp
                        <tr class="cost-row" data-product="{{ $product->id }}" data-price="{{ $product->unit_price }}" style="{{ $pivot ? '' : 'display:none' }}">
                            <td class="py-2">
                                <div class="fw-medium small">{{ $product->name }}</div>
                                <small class="text-muted">{{ $product->unit }}</small>
                            </td>
                            <td>
                                <input type="number" name="products[{{ $product->id }}][quantity]"
                                    value="{{ old("products.{$product->id}.quantity", $pivot?->pivot->quantity ?? '') }}"
                                    class="form-control form-control-sm qty-input" step="0.5" min="0">
                            </td>
                            <td>
                                <input type="number" name="products[{{ $product->id }}][unit_price]"
                                    value="{{ old("products.{$product->id}.unit_price", $pivot?->pivot->unit_price ?? $product->unit_price) }}"
                                    class="form-control form-control-sm price-input" step="0.01" min="0">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-link text-danger p-0 remove-row"><i class="bi bi-x"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                <select id="add-product-select" class="form-select form-select-sm mb-2">
                    <option value="">— Add product —</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} (£{{ $product->unit_price }}/{{ $product->unit }})</option>
                    @endforeach
                </select>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Total:</small>
                    <strong id="cost-total">£0.00</strong>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function recalcTotal() {
    let total = 0;
    document.querySelectorAll('.cost-row:not([style*="none"])').forEach(row => {
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        total += qty * price;
    });
    document.getElementById('cost-total').textContent = '£' + total.toFixed(2);
}

document.getElementById('add-product-select').addEventListener('change', function() {
    const id = this.value;
    if (!id) return;
    const row = document.querySelector(`.cost-row[data-product="${id}"]`);
    if (row) {
        row.style.display = '';
        const qtyInput = row.querySelector('.qty-input');
        if (!qtyInput.value) qtyInput.value = 1;
    }
    this.value = '';
    recalcTotal();
});

document.querySelectorAll('.remove-row').forEach(btn => {
    btn.addEventListener('click', function() {
        const row = this.closest('.cost-row');
        row.style.display = 'none';
        row.querySelector('.qty-input').value = '';
        recalcTotal();
    });
});

document.querySelectorAll('.qty-input, .price-input').forEach(input => {
    input.addEventListener('input', recalcTotal);
});

recalcTotal();
</script>
@endpush
