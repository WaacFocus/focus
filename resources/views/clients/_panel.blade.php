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

    <form id="clientForm" action="{{ route('clients.store') }}" method="POST" novalidate autocomplete="off"
          style="display:flex;flex-direction:column;flex:1 1 auto;min-height:0;">
        @csrf
        <input type="hidden" name="_method" value="">
        <input type="hidden" name="directors_json" value="">

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
                <div class="col-2">
                    <label class="form-label small fw-semibold">Title</label>
                    <select name="contact_title" class="form-select form-select-sm">
                        <option value=""></option>
                        <option value="Mr">Mr</option>
                        <option value="Mrs">Mrs</option>
                        <option value="Miss">Miss</option>
                        <option value="Ms">Ms</option>
                        <option value="Dr">Dr</option>
                        <option value="Prof">Prof</option>
                        <option value="Sir">Sir</option>
                        <option value="Rev">Rev</option>
                    </select>
                </div>
                <div class="col-5">
                    <label class="form-label small fw-semibold">First Name</label>
                    <input type="text" name="contact_first_name" class="form-control form-control-sm" placeholder="First name">
                    <div class="invalid-feedback" data-field="contact_first_name"></div>
                </div>
                <div class="col-5">
                    <label class="form-label small fw-semibold">Surname</label>
                    <input type="text" name="contact_last_name" class="form-control form-control-sm" placeholder="Surname">
                    <div class="invalid-feedback" data-field="contact_last_name"></div>
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

            {{-- Companies House Data --}}
            <p class="text-muted small fw-semibold text-uppercase mb-2" style="letter-spacing:.05em;">Companies House Data</p>
            <div class="row g-3 mb-3">
                <div class="col-4">
                    <label class="form-label small fw-semibold">CH Status</label>
                    <input type="text" name="ch_status" class="form-control form-control-sm" placeholder="active">
                </div>
                <div class="col-4">
                    <label class="form-label small fw-semibold">Incorporated</label>
                    <input type="date" name="ch_incorporated_on" class="form-control form-control-sm">
                </div>
                <div class="col-4">
                    <label class="form-label small fw-semibold">Jurisdiction</label>
                    <input type="text" name="ch_jurisdiction" class="form-control form-control-sm" placeholder="england-wales">
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">SIC Codes</label>
                    <input type="text" name="ch_sic_codes" class="form-control form-control-sm" placeholder="e.g. 69201, 69202">
                </div>
                <div class="col-12"><label class="form-label small fw-semibold mb-1">Registered Address</label></div>
                <div class="col-12">
                    <input type="text" name="ch_reg_address_line_1" class="form-control form-control-sm mb-1" placeholder="Address line 1">
                    <input type="text" name="ch_reg_address_line_2" class="form-control form-control-sm" placeholder="Address line 2">
                </div>
                <div class="col-4">
                    <input type="text" name="ch_reg_locality" class="form-control form-control-sm" placeholder="Town">
                </div>
                <div class="col-4">
                    <input type="text" name="ch_reg_region" class="form-control form-control-sm" placeholder="County / Region">
                </div>
                <div class="col-4">
                    <input type="text" name="ch_reg_postcode" class="form-control form-control-sm" placeholder="Postcode">
                </div>
                <div class="col-6">
                    <input type="text" name="ch_reg_country" class="form-control form-control-sm" placeholder="Country">
                </div>
                <div class="col-4">
                    <label class="form-label small fw-semibold">Accounts Year End</label>
                    <input type="date" name="ch_accounts_year_end" class="form-control form-control-sm">
                </div>
                <div class="col-4">
                    <label class="form-label small fw-semibold">Accounts Due</label>
                    <input type="date" name="ch_accounts_next_due" class="form-control form-control-sm">
                </div>
                <div class="col-4">
                    <label class="form-label small fw-semibold">Conf. Statement Due</label>
                    <input type="date" name="ch_confirmation_statement_next_due" class="form-control form-control-sm">
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

{{-- Companies House confirmation modal --}}
<div class="modal fade" id="chConfirmModal" tabindex="-1" style="z-index:1100;">
    <div class="modal-backdrop-fix"></div>
    <div class="modal-dialog modal-dialog-scrollable" style="max-width:600px;">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:var(--brand-dark,#0C3D38);">
                <div>
                    <h5 class="modal-title mb-0"><i class="bi bi-building-check me-2"></i><span id="chModalCompanyName">Set up new client?</span></h5>
                    <small class="opacity-75">Review the details below, then confirm to populate the form</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3 px-4" id="chModalBody">
                {{-- filled dynamically --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="chConfirmSetupBtn">
                    <i class="bi bi-person-plus me-1"></i>Set up client
                </button>
            </div>
        </div>
    </div>
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

    // Pending data from CH — stored here until user confirms modal
    let chPendingData      = null;
    let chPendingOfficers  = [];
    const chModal          = new bootstrap.Modal(document.getElementById('chConfirmModal'), { backdrop: true });
    const chConfirmBtn     = document.getElementById('chConfirmSetupBtn');

    function chTypeLabel(type) {
        const map = {
            'private-limited-company': 'Private Limited Company',
            'private-limited-guarant-nsc': 'Private Limited by Guarantee',
            'public-limited-company': 'Public Limited Company (PLC)',
            'limited-liability-partnership': 'Limited Liability Partnership',
            'limited-partnership': 'Limited Partnership',
            'scottish-partnership': 'Scottish Partnership',
            'plc': 'PLC',
            'ltd': 'Ltd',
        };
        return map[type] || (type ? type.replace(/-/g,' ').replace(/\b\w/g,c=>c.toUpperCase()) : '');
    }

    function chJurisdictionLabel(j) {
        const map = {
            'england-wales': 'England & Wales',
            'scotland': 'Scotland',
            'northern-ireland': 'Northern Ireland',
            'england': 'England',
            'wales': 'Wales',
            'united-kingdom': 'United Kingdom',
        };
        return map[j] || (j ? j.replace(/-/g,' ').replace(/\b\w/g,c=>c.toUpperCase()) : '');
    }

    function chRoleLabel(role) {
        return (role||'').replace(/-/g,' ').replace(/\b\w/g,c=>c.toUpperCase());
    }

    window.chSelectCompany = async function (number, btn) {
        chHideResults();
        btn.disabled = true;
        chError.classList.add('d-none');

        // Fetch profile and officers in parallel
        try {
            const [profileRes, officersRes] = await Promise.all([
                fetch(`/api/companies-house/${encodeURIComponent(number)}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                }),
                fetch(`/api/companies-house/${encodeURIComponent(number)}/officers`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                }),
            ]);

            const profile  = await profileRes.json();
            const officers = await officersRes.json();

            if (!profileRes.ok) {
                chError.textContent = profile.error ?? 'Could not load company profile.';
                chError.classList.remove('d-none');
                btn.disabled = false;
                return;
            }

            chPendingData     = profile;
            chPendingOfficers = officers.officers ?? [];

            // Build modal body
            const addressParts = [
                profile.ch_reg_address_line_1,
                profile.ch_reg_address_line_2,
                profile.ch_reg_locality,
                profile.ch_reg_region,
                profile.ch_reg_postcode,
                profile.ch_reg_country,
            ].filter(Boolean);
            const statusBadge  = profile.company_status === 'active'
                ? `<span class="badge bg-success">${profile.company_status}</span>`
                : `<span class="badge bg-secondary">${(profile.company_status||'').replace(/-/g,' ')}</span>`;

            let officersHtml = '';
            if (chPendingOfficers.length) {
                officersHtml = `
                    <h6 class="fw-semibold mb-2 mt-3"><i class="bi bi-people me-1"></i>Current Officers (${chPendingOfficers.length})</h6>
                    <p class="small text-muted mb-2">Tick any officer to also create them as an individual client record.</p>
                    <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle" style="font-size:.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Appointed</th>
                                <th class="text-center" title="Set as main contact for the company">Contact?</th>
                                <th class="text-center" title="Self Assessment required">SA?</th>
                                <th>Create as client?</th>
                            </tr>
                        </thead>
                        <tbody>
                        ${chPendingOfficers.map((o, i) => `
                            <tr data-officer="${i}">
                                <td class="fw-semibold">${o.name}</td>
                                <td class="text-muted">${chRoleLabel(o.role)}</td>
                                <td class="text-muted">${o.appointed_on ? new Date(o.appointed_on).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'}) : '—'}</td>
                                <td class="text-center">
                                    <input type="radio" class="form-check-input officer-contact-rb"
                                           name="chMainContact" value="${i}">
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input officer-sa-cb" value="${i}">
                                </td>
                                <td>
                                    <div class="form-check mb-1">
                                        <input class="form-check-input officer-create-cb" type="checkbox"
                                               id="occ_${i}" onchange="toggleOfficerCode(this)">
                                        <label class="form-check-label small text-muted" for="occ_${i}">Yes</label>
                                    </div>
                                    <div class="officer-client-code" style="display:none;min-width:110px;">
                                        <input type="text" class="form-control form-control-sm"
                                               placeholder="Client code" maxlength="50">
                                    </div>
                                </td>
                            </tr>
                        `).join('')}
                        </tbody>
                    </table>
                    </div>`;
            } else {
                officersHtml = `<p class="text-muted small mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>No active officers found.</p>`;
            }

            document.getElementById('chModalCompanyName').textContent = profile.company_name;
            document.getElementById('chModalBody').innerHTML = `
                <div class="row g-0">
                    <div class="col-12">
                        <div class="mb-3 p-3 rounded" style="background:#f0f4ff;border:1px solid #c7d7fb;">
                            <label class="form-label small fw-semibold mb-1">Client Code for <strong>${profile.company_name}</strong> <span class="text-danger">*</span></label>
                            <input type="text" id="chCompanyClientCode" class="form-control form-control-sm"
                                   placeholder="e.g. LTD001" maxlength="50" autocomplete="off">
                            <div class="invalid-feedback">Please enter a client code.</div>
                        </div>
                        <table class="table table-sm mb-0" style="font-size:.85rem;">
                            <tbody>
                                <tr><td class="text-muted" style="width:38%;">Company Number</td><td><strong>${profile.company_number}</strong></td></tr>
                                <tr><td class="text-muted">Type</td><td>${chTypeLabel(profile.company_type)}</td></tr>
                                <tr><td class="text-muted">Status</td><td>${statusBadge}</td></tr>
                                ${addressParts.length ? `<tr><td class="text-muted">Registered Address</td><td>${addressParts.join(', ')}</td></tr>` : ''}
                                ${profile.ch_incorporated_on ? `<tr><td class="text-muted">Incorporated</td><td>${new Date(profile.ch_incorporated_on).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'})}</td></tr>` : ''}
                                ${profile.ch_jurisdiction ? `<tr><td class="text-muted">Jurisdiction</td><td>${chJurisdictionLabel(profile.ch_jurisdiction)}</td></tr>` : ''}
                                ${profile.ch_sic_codes ? `<tr><td class="text-muted">SIC Codes</td><td>${profile.ch_sic_codes}</td></tr>` : ''}
                            </tbody>
                        </table>
                        ${officersHtml}
                    </div>
                </div>`;

            chModal.show();

        } catch (err) {
            chError.textContent = 'Failed to load company details.';
            chError.classList.remove('d-none');
            btn.disabled = false;
        }
    };

    chConfirmBtn.addEventListener('click', function () {
        if (!chPendingData) return;
        const data = chPendingData;

        // Collect all per-officer selections before closing the modal
        const selectedContactIdx = (() => {
            const rb = document.querySelector('#chModalBody input[name="chMainContact"]:checked');
            return rb ? parseInt(rb.value) : -1;
        })();

        const officersWithFlags = chPendingOfficers.map((o, i) => {
            const row       = document.querySelector(`#chModalBody tr[data-officer="${i}"]`);
            const cb        = row ? row.querySelector('.officer-create-cb') : null;
            const saCb      = row ? row.querySelector('.officer-sa-cb') : null;
            const codeInput = row ? row.querySelector('.officer-client-code input') : null;
            return Object.assign({}, o, {
                create_as_client: cb ? cb.checked : false,
                sa_required:      saCb ? saCb.checked : false,
                client_code:      codeInput ? codeInput.value.trim() : '',
            });
        });

        // Validate: company client code is required
        const companyCodeInput = document.getElementById('chCompanyClientCode');
        const companyCode = companyCodeInput ? companyCodeInput.value.trim() : '';
        if (!companyCode) {
            if (companyCodeInput) { companyCodeInput.classList.add('is-invalid'); companyCodeInput.focus(); }
            return;
        }
        if (companyCodeInput) companyCodeInput.classList.remove('is-invalid');

        // Validate: every checked officer must have a client code
        const missing = officersWithFlags.filter(o => o.create_as_client && !o.client_code);
        if (missing.length) {
            missing.forEach((o, i) => {
                const idx   = chPendingOfficers.indexOf(chPendingOfficers.find(p => p.name === o.name));
                const row   = document.querySelector(`#chModalBody tr[data-officer="${idx}"]`);
                const input = row ? row.querySelector('.officer-client-code input') : null;
                if (input) { input.classList.add('is-invalid'); input.focus(); }
            });
            return;
        }

        // Populate contact fields from selected main contact
        if (selectedContactIdx >= 0 && chPendingOfficers[selectedContactIdx]) {
            const contact    = chPendingOfficers[selectedContactIdx];
            const nameParts  = contact.name.trim().split(' ');
            const lastName   = nameParts.length > 1 ? nameParts.pop() : '';
            const firstName  = nameParts.join(' ');
            setField('contact_first_name', firstName);
            setField('contact_last_name',  lastName);
        }

        // Populate form fields
        setField('client_code',    companyCode);
        setField('company_name',   data.company_name);
        setField('company_number', data.company_number);
        if (data.address)  setField('address',  data.address);
        if (data.town)     setField('town',     data.town);
        if (data.county)   setField('county',   data.county);
        if (data.postcode) setField('postcode', data.postcode);

        // CH data fields
        if (data.ch_status)                          setField('ch_status',                          data.ch_status);
        if (data.ch_incorporated_on)                 setField('ch_incorporated_on',                 data.ch_incorporated_on);
        if (data.ch_jurisdiction)                    setField('ch_jurisdiction',                    data.ch_jurisdiction);
        if (data.ch_sic_codes)                       setField('ch_sic_codes',                       data.ch_sic_codes);
        if (data.ch_reg_address_line_1) setField('ch_reg_address_line_1', data.ch_reg_address_line_1);
        if (data.ch_reg_address_line_2) setField('ch_reg_address_line_2', data.ch_reg_address_line_2);
        if (data.ch_reg_locality)       setField('ch_reg_locality',       data.ch_reg_locality);
        if (data.ch_reg_region)         setField('ch_reg_region',         data.ch_reg_region);
        if (data.ch_reg_postcode)       setField('ch_reg_postcode',       data.ch_reg_postcode);
        if (data.ch_reg_country)        setField('ch_reg_country',        data.ch_reg_country);
        if (data.ch_accounts_year_end)               setField('ch_accounts_year_end',               data.ch_accounts_year_end);
        if (data.ch_accounts_next_due)               setField('ch_accounts_next_due',               data.ch_accounts_next_due);
        if (data.ch_confirmation_statement_next_due) setField('ch_confirmation_statement_next_due', data.ch_confirmation_statement_next_due);

        // Auto-select client type
        const chTypeName = chTypeMap[(data.company_type ?? '').toLowerCase()];
        if (chTypeName && clientTypeMap[chTypeName]) {
            setField('client_type_id', clientTypeMap[chTypeName]);
        }

        // Store directors (with create_as_client flags) as JSON for form submission
        const dirJson = form.querySelector('[name="directors_json"]');
        if (dirJson) dirJson.value = JSON.stringify(officersWithFlags);

        // Show confirmation banner
        const toCreate = officersWithFlags.filter(o => o.create_as_client && o.client_code).length;
        const officerNote = officersWithFlags.length
            ? ` · ${officersWithFlags.length} officer(s) saved as directors${toCreate ? `, ${toCreate} also created as client(s)` : ''}`
            : '';
        document.getElementById('chSelectedText').textContent =
            `${data.company_name} (${data.company_number}) populated${officerNote}`;
        chSelected.classList.remove('d-none');
        chInput.value = '';
        chPendingData     = null;
        chPendingOfficers = [];
        chModal.hide();
    });

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

        // Edit mode — show lookup for re-syncing
        chLookup.style.display = '';
        panelEl.querySelector('#chLookup p span.fw-normal').textContent = '— re-sync to overwrite with latest data';

        panelEl.querySelector('#panelIcon').className = 'bi bi-arrow-clockwise me-2';
        panelEl.querySelector('#panelTitle').textContent = 'Loading…';
        panelEl.querySelector('#panelSubtitle').textContent = '';
        bsPanel.show();

        try {
            const res  = await fetch(`/clients/${clientId}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            });
            const data = await res.json();

            ['client_code','company_name','client_type_id','status','account_manager',
             'contact_title','contact_first_name','contact_last_name',
             'email','phone','address','town','county','postcode',
             'fpa_amount','billing_interval','payment_method',
             'vat_number','company_number','utr_number','paye_ref','notes',
             'ch_status','ch_jurisdiction','ch_sic_codes',
             'ch_reg_address_line_1','ch_reg_address_line_2','ch_reg_locality',
             'ch_reg_region','ch_reg_postcode','ch_reg_country']
                .forEach(f => setField(f, data[f]));

            ['ch_incorporated_on','ch_accounts_year_end','ch_accounts_next_due','ch_confirmation_statement_next_due']
                .forEach(f => setField(f, data[f] ? data[f].substring(0, 10) : ''));

            setField('fpa_year_end', data.fpa_year_end ? data.fpa_year_end.substring(0, 10) : '');

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

    window.toggleOfficerCode = function (checkbox) {
        const codeWrap = checkbox.closest('tr').querySelector('.officer-client-code');
        codeWrap.style.display = checkbox.checked ? '' : 'none';
        const input = codeWrap.querySelector('input');
        input.classList.remove('is-invalid');
        if (checkbox.checked) input.focus();
        else input.value = '';
    };

    // Chromium (Edge/Chrome) ignores autocomplete="off" on address-like fields.
    // Setting an unrecognised token prevents their autofill heuristics.
    function disableAutofill() {
        form.querySelectorAll('input, select, textarea').forEach((el, i) => {
            el.setAttribute('autocomplete', 'focus-off-' + i);
        });
    }
    disableAutofill();
    panelEl.addEventListener('shown.bs.offcanvas', disableAutofill);

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
