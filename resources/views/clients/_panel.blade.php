{{-- Shared client create / edit offcanvas panel --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="clientPanel" style="width: 560px;">
    <div class="offcanvas-header border-bottom" style="background: var(--brand-dark, #0C3D38);">
        <div>
            <h5 class="offcanvas-title text-white mb-0">
                <i id="panelIcon" class="bi bi-person-plus me-2"></i><span id="panelTitle">New Client</span>
            </h5>
            <small id="panelSubtitle" class="text-white-50">Fill in the details below and save</small>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>

    <form id="clientForm" action="{{ route('clients.store') }}" method="POST" novalidate
          style="display:flex;flex-direction:column;flex:1 1 auto;min-height:0;">
        @csrf
        <input type="hidden" name="_method" value="">

        <div class="offcanvas-body">

            <div id="formSuccess" class="alert alert-success d-none py-2 small mb-3">
                <i class="bi bi-check-circle me-1"></i><span id="successMsg"></span>
            </div>

            {{-- Companies House Lookup --}}
            <div id="chLookup" class="mb-3 p-3 rounded" style="background:#f0f4ff;border:1px solid #c7d7fb;">
                <p class="fw-semibold small mb-2" style="color:var(--brand-dark, #0C3D38);">
                    <i class="bi bi-search me-1"></i>Companies House Lookup
                    <span class="fw-normal text-muted ms-1">— search to pre-fill the form</span>
                </p>
                <div class="input-group input-group-sm">
                    <input type="text" id="chSearchInput" class="form-control"
                           placeholder="Search by company name or number…"
                           autocomplete="off">
                    <button type="button" class="btn btn-primary" id="chSearchBtn">
                        <span id="chBtnSpinner" class="spinner-border spinner-border-sm d-none me-1"></span>
                        <span id="chBtnText">Search</span>
                    </button>
                </div>
                <div id="chResults" class="list-group mt-1 d-none" style="max-height:220px;overflow-y:auto;border-radius:.375rem;box-shadow:0 4px 12px rgba(0,0,0,.1);"></div>
                <div id="chSelected" class="alert alert-success d-none py-2 small mt-2 mb-0">
                    <i class="bi bi-building-check me-1"></i><span id="chSelectedText"></span>
                    <button type="button" class="btn-close float-end btn-close-sm" style="font-size:.65rem;" onclick="clearChSelection()"></button>
                </div>
                <div id="chError" class="text-danger small d-none mt-1"></div>
            </div>

            {{-- Core Details --}}
            <p class="text-muted small fw-semibold text-uppercase mb-2" style="letter-spacing:.05em;">Core Details</p>
            <div class="row g-3 mb-3">
                <div class="col-4">
                    <label class="form-label small fw-semibold">Client Code <span class="text-danger">*</span></label>
                    <input type="text" name="client_code" class="form-control form-control-sm" placeholder="CLT001" required>
                    <div class="invalid-feedback" data-field="client_code"></div>
                </div>
                <div class="col-8">
                    <label class="form-label small fw-semibold">Company Name <span class="text-danger">*</span></label>
                    <input type="text" name="company_name" class="form-control form-control-sm" required>
                    <div class="invalid-feedback" data-field="company_name"></div>
                </div>
                <div class="col-6">
                    <label class="form-label small fw-semibold">Client Type <span class="text-danger">*</span></label>
                    <select name="client_type_id" class="form-select form-select-sm" required>
                        <option value="">— Select type —</option>
                        @foreach($clientTypes ?? [] as $ct)
                            <option value="{{ $ct->id }}">{{ $ct->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" data-field="client_type_id"></div>
                </div>
                <div class="col-6">
                    <label class="form-label small fw-semibold">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="active">Active</option>
                        <option value="prospect">Prospect</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label small fw-semibold">Account Manager</label>
                    <input type="text" name="account_manager" class="form-control form-control-sm" placeholder="Name">
                    <div class="invalid-feedback" data-field="account_manager"></div>
                </div>
                <div class="col-6">
                    <label class="form-label small fw-semibold">Contact Name</label>
                    <input type="text" name="contact_name" class="form-control form-control-sm" placeholder="Name">
                    <div class="invalid-feedback" data-field="contact_name"></div>
                </div>
                <div class="col-6">
                    <label class="form-label small fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control form-control-sm" placeholder="email@example.com">
                    <div class="invalid-feedback" data-field="email"></div>
                </div>
                <div class="col-6">
                    <label class="form-label small fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control form-control-sm" placeholder="01234 567890">
                    <div class="invalid-feedback" data-field="phone"></div>
                </div>
            </div>

            <hr class="my-3">

            {{-- Fixed Price Agreement --}}
            <p class="text-muted small fw-semibold text-uppercase mb-2" style="letter-spacing:.05em;">Fixed Price Agreement</p>
            <div class="row g-3 mb-3">
                <div class="col-4">
                    <label class="form-label small fw-semibold">FPA Amount (£)</label>
                    <input type="number" name="fpa_amount" step="0.01" min="0" class="form-control form-control-sm" placeholder="0.00">
                    <div class="invalid-feedback" data-field="fpa_amount"></div>
                </div>
                <div class="col-4">
                    <label class="form-label small fw-semibold">Billing Interval</label>
                    <select name="billing_interval" class="form-select form-select-sm">
                        <option value="">— None —</option>
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="annually">Annually</option>
                        <option value="one-off">One-off</option>
                    </select>
                </div>
                <div class="col-4">
                    <label class="form-label small fw-semibold">FPA Year End</label>
                    <input type="date" name="fpa_year_end" class="form-control form-control-sm">
                    <div class="invalid-feedback" data-field="fpa_year_end"></div>
                </div>
            </div>

            {{-- Additional billing lines — live within FPA section --}}
            <div class="row g-1 mb-1 d-none d-billing-header">
                <div class="col-5"><label class="form-label small fw-semibold mb-0">Description</label></div>
                <div class="col-3"><label class="form-label small fw-semibold mb-0">Amount (£)</label></div>
                <div class="col-3"><label class="form-label small fw-semibold mb-0">Interval</label></div>
                <div class="col-1"></div>
            </div>
            <div id="billingLinesContainer"></div>
            <button type="button" class="btn btn-sm btn-outline-secondary mt-1 mb-3" onclick="addBillingLine()">
                <i class="bi bi-plus-lg me-1"></i>Add Line
            </button>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label small fw-semibold">Payment Method</label>
                    <select name="payment_method" class="form-select form-select-sm">
                        <option value="">— None —</option>
                        <option value="Direct Debit">Direct Debit</option>
                        <option value="BACS">BACS</option>
                        <option value="Standing Order">Standing Order</option>
                        <option value="Credit/Debit Card">Credit/Debit Card</option>
                        <option value="Cash">Cash</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                    <div class="invalid-feedback" data-field="payment_method"></div>
                </div>
            </div>

            <hr class="my-3">

            {{-- Payroll --}}
            <p class="text-muted small fw-semibold text-uppercase mb-2" style="letter-spacing:.05em;">Payroll</p>
            <div class="row g-3 mb-3">
                <div class="col-4">
                    <label class="form-label small fw-semibold">Payroll FPA (£)</label>
                    <input type="number" name="payroll_fpa" step="0.01" min="0" class="form-control form-control-sm" placeholder="0.00">
                    <div class="invalid-feedback" data-field="payroll_fpa"></div>
                </div>
                <div class="col-4">
                    <label class="form-label small fw-semibold">Payroll Interval</label>
                    <select name="payroll_billing_interval" class="form-select form-select-sm">
                        <option value="">— None —</option>
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="annually">Annually</option>
                        <option value="one-off">One-off</option>
                    </select>
                </div>
            </div>

            <hr class="my-3">

            {{-- Tax & Regulatory --}}
            <p class="text-muted small fw-semibold text-uppercase mb-2" style="letter-spacing:.05em;">Tax & Regulatory</p>
            <div class="row g-3 mb-3">
                <div class="col-4">
                    <label class="form-label small fw-semibold">VAT Number</label>
                    <input type="text" name="vat_number" class="form-control form-control-sm" placeholder="GB123456789">
                </div>
                <div class="col-4">
                    <label class="form-label small fw-semibold">Company No.</label>
                    <input type="text" name="company_number" class="form-control form-control-sm" placeholder="12345678">
                </div>
                <div class="col-4">
                    <label class="form-label small fw-semibold">UTR</label>
                    <input type="text" name="utr_number" class="form-control form-control-sm" placeholder="1234567890">
                </div>
                <div class="col-4">
                    <label class="form-label small fw-semibold">PAYE Reference</label>
                    <input type="text" name="paye_ref" class="form-control form-control-sm" placeholder="123/AB456">
                </div>
            </div>

            <hr class="my-3">

            {{-- Notes --}}
            <p class="text-muted small fw-semibold text-uppercase mb-2" style="letter-spacing:.05em;">Notes</p>
            <textarea name="notes" rows="4" class="form-control form-control-sm mb-3" placeholder="Any additional notes..."></textarea>

        </div>

        <div class="offcanvas-footer border-top d-flex gap-2 p-3 bg-white">
            <button type="submit" id="saveClientBtn" class="btn btn-primary flex-grow-1">
                <span class="btn-label"><i class="bi bi-check-lg me-1"></i><span id="saveBtnText">Create Client</span></span>
                <span class="btn-spinner d-none"><span class="spinner-border spinner-border-sm me-1"></span>Saving…</span>
            </button>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function () {
    const panelEl  = document.getElementById('clientPanel');
    const bsPanel  = new bootstrap.Offcanvas(panelEl);
    const form     = document.getElementById('clientForm');
    const saveBtn  = document.getElementById('saveClientBtn');
    const csrf     = document.querySelector('meta[name="csrf-token"]').content;
    const storeUrl = '{{ route("clients.store") }}';

    // ── Client type name → id map (populated from PHP) ──────────────────────
    const clientTypeMap = {};
    @foreach($clientTypes ?? [] as $ct)
    clientTypeMap[{!! json_encode(strtolower($ct->name)) !!}] = {{ $ct->id }};
    @endforeach

    // Maps Companies House company_type values to our client type names
    const chTypeMap = {
        'ltd': 'limited company',
        'private-limited-company': 'limited company',
        'private-limited-guarant-nsc': 'limited company',
        'private-limited-guarant-nsc-limited-exemption': 'limited company',
        'private-unlimited': 'limited company',
        'public-limited-company': 'limited company',
        'plc': 'limited company',
        'llp': 'partnership',
        'limited-liability-partnership': 'partnership',
        'limited-partnership': 'partnership',
        'scottish-partnership': 'partnership',
        'sole-trader': 'sole trader',
    };

    // ── Helpers ──────────────────────────────────────────────────────────────
    function setLoading(on) {
        saveBtn.disabled = on;
        saveBtn.querySelector('.btn-label').classList.toggle('d-none', on);
        saveBtn.querySelector('.btn-spinner').classList.toggle('d-none', !on);
    }

    function clearErrors() {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('[data-field]').forEach(el => el.textContent = '');
        document.getElementById('formSuccess').classList.add('d-none');
    }

    function showErrors(errors) {
        Object.entries(errors).forEach(([field, msgs]) => {
            const input    = form.querySelector(`[name="${field}"]`);
            const feedback = form.querySelector(`[data-field="${field}"]`);
            if (input)    input.classList.add('is-invalid');
            if (feedback) feedback.textContent = msgs[0];
        });
        const first = form.querySelector('.is-invalid');
        if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function setField(name, value) {
        const el = form.querySelector(`[name="${name}"]`);
        if (!el) return;
        if (el.type === 'checkbox') {
            el.checked = !!value;
        } else {
            el.value = value ?? '';
        }
    }

    // ── Companies House lookup ────────────────────────────────────────────────
    const chInput     = document.getElementById('chSearchInput');
    const chBtn       = document.getElementById('chSearchBtn');
    const chResults   = document.getElementById('chResults');
    const chSelected  = document.getElementById('chSelected');
    const chError     = document.getElementById('chError');
    const chLookup    = document.getElementById('chLookup');
    let   chTimer;

    function chSetSearching(on) {
        document.getElementById('chBtnSpinner').classList.toggle('d-none', !on);
        document.getElementById('chBtnText').textContent = on ? 'Searching…' : 'Search';
        chBtn.disabled = on;
    }

    function chHideResults() {
        chResults.classList.add('d-none');
        chResults.innerHTML = '';
    }

    window.clearChSelection = function () {
        chSelected.classList.add('d-none');
        chInput.value = '';
        chHideResults();
    };

    async function chDoSearch() {
        const q = chInput.value.trim();
        if (q.length < 2) { chHideResults(); return; }

        chSetSearching(true);
        chError.classList.add('d-none');
        chHideResults();

        try {
            const res  = await fetch(`/api/companies-house/search?q=${encodeURIComponent(q)}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            });
            const data = await res.json();

            if (!res.ok || data.error) {
                chError.textContent = data.error ?? 'Search failed. Check your API key.';
                chError.classList.remove('d-none');
                return;
            }

            if (!data.items?.length) {
                chResults.innerHTML = '<div class="list-group-item text-muted small">No results found.</div>';
                chResults.classList.remove('d-none');
                return;
            }

            chResults.innerHTML = data.items.map(item => `
                <button type="button" class="list-group-item list-group-item-action small py-2"
                        onclick="chSelectCompany('${item.company_number}', this)">
                    <div class="fw-semibold">${item.title}</div>
                    <div class="text-muted" style="font-size:.8rem;">
                        ${item.company_number}
                        ${item.company_status ? '· ' + item.company_status.replace(/-/g,' ') : ''}
                        ${item.address_snippet ? '· ' + item.address_snippet : ''}
                    </div>
                </button>
            `).join('');
            chResults.classList.remove('d-none');

        } catch (err) {
            chError.textContent = 'Search failed. Please try again.';
            chError.classList.remove('d-none');
        } finally {
            chSetSearching(false);
        }
    }

    window.chSelectCompany = async function (number, btn) {
        chHideResults();
        btn.disabled = true;

        try {
            const res  = await fetch(`/api/companies-house/${encodeURIComponent(number)}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            });
            const data = await res.json();

            if (!res.ok) {
                chError.textContent = data.error ?? 'Could not load company profile.';
                chError.classList.remove('d-none');
                return;
            }

            // Populate form fields
            setField('company_name', data.company_name);
            setField('company_number', data.company_number);
            if (data.address)  setField('address',  data.address);
            if (data.town)     setField('town',     data.town);
            if (data.county)   setField('county',   data.county);
            if (data.postcode) setField('postcode', data.postcode);

            // Auto-select client type
            const chTypeName = chTypeMap[(data.company_type ?? '').toLowerCase()];
            if (chTypeName && clientTypeMap[chTypeName]) {
                setField('client_type_id', clientTypeMap[chTypeName]);
            }

            // Show selected confirmation
            document.getElementById('chSelectedText').textContent =
                `${data.company_name} (${data.company_number}) populated into form`;
            chSelected.classList.remove('d-none');
            chInput.value = '';

        } catch (err) {
            chError.textContent = 'Failed to load company details.';
            chError.classList.remove('d-none');
        }
    };

    chInput.addEventListener('input', function () {
        clearTimeout(chTimer);
        chTimer = setTimeout(chDoSearch, 350);
    });
    chBtn.addEventListener('click', chDoSearch);
    chInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); clearTimeout(chTimer); chDoSearch(); }
    });

    // Hide results when clicking outside
    document.addEventListener('click', function (e) {
        if (!chResults.contains(e.target) && e.target !== chInput && e.target !== chBtn) {
            chHideResults();
        }
    });

    // ── Panel open/close ─────────────────────────────────────────────────────
    window.openClientPanel = async function (clientId) {
        form.reset();
        clearErrors();
        chHideResults();
        chSelected.classList.add('d-none');
        chError.classList.add('d-none');
        chInput.value = '';

        if (!clientId) {
            panelEl.querySelector('#panelIcon').className = 'bi bi-person-plus me-2';
            panelEl.querySelector('#panelTitle').textContent = 'New Client';
            panelEl.querySelector('#panelSubtitle').textContent = 'Fill in the details below or search Companies House above';
            form.action = storeUrl;
            form.querySelector('[name="_method"]').value = '';
            document.getElementById('saveBtnText').textContent = 'Create Client';
            chLookup.style.display = '';
            clearBillingLines();
            bsPanel.show();
            return;
        }

        // Edit mode — hide CH lookup (data already exists)
        chLookup.style.display = 'none';

        panelEl.querySelector('#panelIcon').className = 'bi bi-arrow-clockwise me-2';
        panelEl.querySelector('#panelTitle').textContent = 'Loading…';
        panelEl.querySelector('#panelSubtitle').textContent = '';
        bsPanel.show();

        try {
            const res  = await fetch(`/clients/${clientId}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            });
            const data = await res.json();

            ['client_code','company_name','client_type_id','status','account_manager','contact_name',
             'email','phone','address','town','county','postcode',
             'fpa_amount','billing_interval','payment_method',
             'payroll_fpa','payroll_billing_interval',
             'vat_number','company_number','utr_number','paye_ref','notes']
                .forEach(f => setField(f, data[f]));

            setField('fpa_year_end', data.fpa_year_end ? data.fpa_year_end.substring(0, 10) : '');
            setField('sa_billed_separately',       data.sa_billed_separately);
            setField('payroll_invoiced_separately', data.payroll_invoiced_separately);

            clearBillingLines();
            if (data.billing_lines && data.billing_lines.length) {
                data.billing_lines.forEach(function (line) { addBillingLine(line); });
            }

            panelEl.querySelector('#panelIcon').className = 'bi bi-pencil me-2';
            panelEl.querySelector('#panelTitle').textContent = data.company_name;
            panelEl.querySelector('#panelSubtitle').textContent = 'Edit client details';
            form.action = `/clients/${clientId}`;
            form.querySelector('[name="_method"]').value = 'PATCH';
            document.getElementById('saveBtnText').textContent = 'Save Changes';
        } catch (err) {
            console.error(err);
            bsPanel.hide();
        }
    };

    // ── Form submit ──────────────────────────────────────────────────────────
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();

        // Client-side required field check
        let hasClientErrors = false;
        form.querySelectorAll('[required]').forEach(function (el) {
            if (!el.value.trim()) {
                el.classList.add('is-invalid');
                const fb = form.querySelector('[data-field="' + el.name + '"]');
                if (fb && !fb.textContent) fb.textContent = 'This field is required.';
                hasClientErrors = true;
            }
        });
        if (hasClientErrors) {
            const first = form.querySelector('.is-invalid');
            if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        setLoading(true);

        try {
            const res  = await fetch(form.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: new FormData(form),
            });
            const data = await res.json();

            if (res.status === 422) {
                showErrors(data.errors);
            } else if (res.ok) {
                document.getElementById('successMsg').textContent = data.message;
                document.getElementById('formSuccess').classList.remove('d-none');
                setTimeout(() => window.location.reload(), 800);
            }
        } catch (err) {
            console.error(err);
        } finally {
            setLoading(false);
        }
    });

    panelEl.addEventListener('hidden.bs.offcanvas', function () {
        form.reset();
        clearErrors();
        clearBillingLines();
        chHideResults();
        chSelected.classList.add('d-none');
        chLookup.style.display = '';
    });

    // ── Billing lines ─────────────────────────────────────────────────────────
    const billingContainer = document.getElementById('billingLinesContainer');
    const billingHeader    = document.querySelector('.d-billing-header');

    function updateBillingHeader() {
        const hasLines = billingContainer.querySelectorAll('.billing-line').length > 0;
        billingHeader.classList.toggle('d-none', !hasLines);
    }

    window.clearBillingLines = function () {
        billingContainer.innerHTML = '';
        updateBillingHeader();
    };

    window.addBillingLine = function (data) {
        const idx = billingContainer.querySelectorAll('.billing-line').length;
        const row = document.createElement('div');
        row.className = 'billing-line row g-1 mb-1 align-items-center';
        row.innerHTML =
            '<div class="col-5">' +
                '<input type="text" name="billing_lines[' + idx + '][description]" class="form-control form-control-sm" placeholder="e.g. Monthly bookkeeping" value="' + (data ? (data.description || '') : '') + '">' +
            '</div>' +
            '<div class="col-3">' +
                '<input type="number" name="billing_lines[' + idx + '][amount]" class="form-control form-control-sm" placeholder="0.00" step="0.01" min="0" value="' + (data ? (data.amount || '') : '') + '">' +
            '</div>' +
            '<div class="col-3">' +
                '<select name="billing_lines[' + idx + '][interval]" class="form-select form-select-sm">' +
                    '<option value="monthly"'   + (data && data.interval === 'monthly'   ? ' selected' : '') + '>Monthly</option>' +
                    '<option value="quarterly"' + (data && data.interval === 'quarterly' ? ' selected' : '') + '>Quarterly</option>' +
                    '<option value="annually"'  + (data && data.interval === 'annually'  ? ' selected' : '') + '>Annually</option>' +
                    '<option value="one-off"'   + (data && data.interval === 'one-off'   ? ' selected' : '') + '>One-off</option>' +
                '</select>' +
            '</div>' +
            '<div class="col-1 text-end">' +
                '<button type="button" class="btn btn-sm btn-outline-danger px-1" onclick="removeBillingLine(this)" title="Remove"><i class="bi bi-x-lg" style="font-size:.7rem;"></i></button>' +
            '</div>';
        billingContainer.appendChild(row);
        updateBillingHeader();
    };

    window.removeBillingLine = function (btn) {
        btn.closest('.billing-line').remove();
        // Renumber remaining lines
        billingContainer.querySelectorAll('.billing-line').forEach(function (row, idx) {
            row.querySelectorAll('[name]').forEach(function (el) {
                el.name = el.name.replace(/billing_lines\[\d+\]/, 'billing_lines[' + idx + ']');
            });
        });
        updateBillingHeader();
    };
})();
</script>
@endpush
