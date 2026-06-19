<div class="card shadow-sm">
    <div class="card-body row g-3">
        <div class="col-md-6">
            <label class="form-label">Client <span class="text-danger">*</span></label>
            <select name="client_id" class="form-select @error('client_id') is-invalid @enderror" required>
                <option value="">— Select Client —</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" @selected(old('client_id', $renewal->client_id ?? $selected_client ?? null) == $client->id)>{{ $client->company_name }}</option>
                @endforeach
            </select>
            @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Service (optional)</label>
            <select name="service_id" class="form-select">
                <option value="">— None —</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}" @selected(old('service_id', $renewal->service_id ?? null) == $service->id)>{{ $service->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Description <span class="text-danger">*</span></label>
            <input type="text" name="description" value="{{ old('description', $renewal->description ?? '') }}" class="form-control @error('description') is-invalid @enderror" required placeholder="e.g. Annual Accounts, Self Assessment, Payroll">
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">Renewal Date <span class="text-danger">*</span></label>
            <input type="date" name="renewal_date" value="{{ old('renewal_date', isset($renewal->renewal_date) ? $renewal->renewal_date->format('Y-m-d') : '') }}" class="form-control @error('renewal_date') is-invalid @enderror" required>
            @error('renewal_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">Amount (£)</label>
            <div class="input-group">
                <span class="input-group-text">£</span>
                <input type="number" name="amount" value="{{ old('amount', $renewal->amount ?? '') }}" class="form-control" step="0.01" min="0">
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Billing Cycle</label>
            <select name="billing_cycle" class="form-select">
                @foreach(['monthly' => 'Monthly','quarterly' => 'Quarterly','annually' => 'Annually','one_off' => 'One-off'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('billing_cycle', $renewal->billing_cycle ?? 'annually') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                @foreach(['pending' => 'Pending','renewed' => 'Renewed','cancelled' => 'Cancelled','overdue' => 'Overdue'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('status', $renewal->status ?? 'pending') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Next Renewal Date</label>
            <input type="date" name="next_renewal_date" value="{{ old('next_renewal_date', isset($renewal->next_renewal_date) ? $renewal->next_renewal_date->format('Y-m-d') : '') }}" class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label">Notes</label>
            <textarea name="notes" rows="3" class="form-control">{{ old('notes', $renewal->notes ?? '') }}</textarea>
        </div>
    </div>
</div>
