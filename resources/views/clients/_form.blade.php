{{-- Server-side validation errors --}}
@if($errors->any())
<div class="alert alert-danger mb-4">
    <strong><i class="bi bi-exclamation-triangle me-1"></i>Please fix the following errors:</strong>
    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Company Details</div>
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <label class="form-label">Client Code <span class="text-danger">*</span></label>
                    <input type="text" name="client_code" value="{{ old('client_code', $client->client_code ?? '') }}"
                           class="form-control @error('client_code') is-invalid @enderror"
                           placeholder="e.g. CLT001" required>
                    @error('client_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-5">
                    <label class="form-label">Company Name <span class="text-danger">*</span></label>
                    <input type="text" name="company_name" value="{{ old('company_name', $client->company_name ?? '') }}"
                           class="form-control @error('company_name') is-invalid @enderror"
                           required>
                    @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active"   @selected(old('status', $client->status ?? 'active') === 'active')>Active</option>
                        <option value="prospect" @selected(old('status', $client->status ?? '') === 'prospect')>Prospect</option>
                        <option value="inactive" @selected(old('status', $client->status ?? '') === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Client Type <span class="text-danger">*</span></label>
                    <select name="client_type_id" class="form-select @error('client_type_id') is-invalid @enderror" required>
                        <option value="">— Select Type —</option>
                        @foreach($clientTypes as $type)
                            <option value="{{ $type->id }}" @selected(old('client_type_id', $client->client_type_id ?? '') == $type->id)>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">
                        @error('client_type_id'){{ $message }}@else Please select a client type.@enderror
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Title</label>
                    <select name="contact_title" class="form-select @error('contact_title') is-invalid @enderror">
                        <option value=""></option>
                        @foreach(['Mr','Mrs','Miss','Ms','Dr','Prof','Sir','Rev'] as $t)
                            <option value="{{ $t }}" @selected(old('contact_title', $client->contact_title ?? '') === $t)>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">First Name</label>
                    <input type="text" name="contact_first_name" value="{{ old('contact_first_name', $client->contact_first_name ?? '') }}"
                           class="form-control @error('contact_first_name') is-invalid @enderror" placeholder="First name">
                    @error('contact_first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label">Middle Name</label>
                    <input type="text" name="contact_middle_name" value="{{ old('contact_middle_name', $client->contact_middle_name ?? '') }}"
                           class="form-control @error('contact_middle_name') is-invalid @enderror" placeholder="Middle name">
                    @error('contact_middle_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label">Surname</label>
                    <input type="text" name="contact_last_name" value="{{ old('contact_last_name', $client->contact_last_name ?? '') }}"
                           class="form-control @error('contact_last_name') is-invalid @enderror" placeholder="Surname">
                    @error('contact_last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Account Manager</label>
                    <input type="text" name="account_manager" value="{{ old('account_manager', $client->account_manager ?? '') }}"
                           class="form-control @error('account_manager') is-invalid @enderror"
                           placeholder="Account manager">
                    @error('account_manager')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $client->email ?? '') }}"
                           class="form-control @error('email') is-invalid @enderror">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $client->phone ?? '') }}"
                           class="form-control @error('phone') is-invalid @enderror">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <input type="text" name="premises" value="{{ old('premises', $client->address ?? '') }}"
                           autocomplete="nope"
                           class="form-control @error('premises') is-invalid @enderror"
                           placeholder="Street address">
                    @error('premises')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Town</label>
                    <input type="text" name="premises_town" value="{{ old('premises_town', $client->town ?? '') }}"
                           autocomplete="nope"
                           class="form-control @error('premises_town') is-invalid @enderror">
                    @error('premises_town')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">County</label>
                    <input type="text" name="premises_county" value="{{ old('premises_county', $client->county ?? '') }}"
                           autocomplete="nope"
                           class="form-control @error('premises_county') is-invalid @enderror">
                    @error('premises_county')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Postcode</label>
                    <input type="text" name="premises_postcode" value="{{ old('premises_postcode', $client->postcode ?? '') }}"
                           autocomplete="nope"
                           class="form-control @error('premises_postcode') is-invalid @enderror">
                    @error('premises_postcode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-header bg-white fw-semibold">Fixed Price Agreement (FPA)</div>
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <label class="form-label">FPA Year End</label>
                    <input type="date" name="fpa_year_end"
                        value="{{ old('fpa_year_end', isset($client->fpa_year_end) && $client->fpa_year_end ? $client->fpa_year_end->format('Y-m-d') : '') }}"
                        class="form-control @error('fpa_year_end') is-invalid @enderror">
                    @error('fpa_year_end')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Current FPA Amount (£)</label>
                    <input type="number" name="fpa_amount" step="0.01" min="0"
                        value="{{ old('fpa_amount', $client->fpa_amount ?? '') }}"
                        class="form-control @error('fpa_amount') is-invalid @enderror"
                        placeholder="0.00">
                    @error('fpa_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Billing Interval</label>
                    <select name="billing_interval" class="form-select @error('billing_interval') is-invalid @enderror">
                        <option value="">— Select —</option>
                        <option value="monthly"   @selected(old('billing_interval', $client->billing_interval ?? '') === 'monthly')>Monthly</option>
                        <option value="quarterly" @selected(old('billing_interval', $client->billing_interval ?? '') === 'quarterly')>Quarterly</option>
                        <option value="annually"  @selected(old('billing_interval', $client->billing_interval ?? '') === 'annually')>Annually</option>
                        <option value="one-off"   @selected(old('billing_interval', $client->billing_interval ?? '') === 'one-off')>One-off</option>
                    </select>
                    @error('billing_interval')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Payment Method</label>
                    <input type="text" name="payment_method"
                        value="{{ old('payment_method', $client->payment_method ?? '') }}"
                        class="form-control @error('payment_method') is-invalid @enderror"
                        placeholder="e.g. Direct Debit, BACS">
                    @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-header bg-white fw-semibold">Notes</div>
            <div class="card-body">
                <textarea name="notes" rows="4" class="form-control">{{ old('notes', $client->notes ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Tax & Regulatory</div>
            <div class="card-body row g-3">
                <div class="col-12">
                    <label class="form-label">VAT Number</label>
                    <input type="text" name="vat_number" value="{{ old('vat_number', $client->vat_number ?? '') }}"
                           class="form-control @error('vat_number') is-invalid @enderror"
                           placeholder="GB123456789">
                    @error('vat_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Company Number</label>
                    <input type="text" name="company_number" value="{{ old('company_number', $client->company_number ?? '') }}"
                           class="form-control @error('company_number') is-invalid @enderror"
                           placeholder="12345678">
                    @error('company_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">UTR Number</label>
                    <input type="text" name="utr_number" value="{{ old('utr_number', $client->utr_number ?? '') }}"
                           class="form-control @error('utr_number') is-invalid @enderror"
                           placeholder="1234567890">
                    @error('utr_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">PAYE Reference</label>
                    <input type="text" name="paye_ref" value="{{ old('paye_ref', $client->paye_ref ?? '') }}"
                           class="form-control @error('paye_ref') is-invalid @enderror"
                           placeholder="123/AB456">
                    @error('paye_ref')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
</div>
