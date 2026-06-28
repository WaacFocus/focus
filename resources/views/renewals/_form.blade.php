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
            <label class="form-label">Letter Type <span class="text-danger">*</span></label>
            <input type="text" name="description" value="{{ old('description', $renewal->description ?? '') }}"
                   class="form-control @error('description') is-invalid @enderror"
                   required list="engagementLetterTypes" placeholder="e.g. Accounts & Tax">
            <datalist id="engagementLetterTypes">
                <option value="Accounts &amp; Tax">
                <option value="Self Assessment">
                <option value="Payroll">
                <option value="VAT Returns">
                <option value="Bookkeeping">
                <option value="Company Secretarial">
                <option value="All Services">
            </datalist>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Last Completed</label>
            <input type="date" name="completed_date" value="{{ old('completed_date', isset($renewal->completed_date) ? $renewal->completed_date->format('Y-m-d') : '') }}" class="form-control @error('completed_date') is-invalid @enderror">
            <div class="form-text">Setting this date will mark status as Signed and auto-set next due date.</div>
            @error('completed_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Next Due Date</label>
            <input type="date" name="due_date" value="{{ old('due_date', isset($renewal->due_date) ? $renewal->due_date->format('Y-m-d') : '') }}" class="form-control @error('due_date') is-invalid @enderror">
            <div class="form-text">Auto-filled as 12 months from last completed date.</div>
            @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                @foreach(['pending' => 'Pending','sent' => 'Sent (awaiting signature)','signed' => 'Signed','overdue' => 'Overdue'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('status', $renewal->status ?? 'pending') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Notes</label>
            <textarea name="notes" rows="3" class="form-control">{{ old('notes', $renewal->notes ?? '') }}</textarea>
        </div>
    </div>
</div>
