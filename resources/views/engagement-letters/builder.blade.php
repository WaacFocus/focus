@extends('layouts.app')

@section('title', $letter->exists ? 'Edit Engagement Letter' : 'New Engagement Letter')
@section('page-title', 'Engagement Letters')

@push('styles')
<style>
.section-card { border: 1px solid #dee2e6; border-radius: .375rem; margin-bottom: .5rem; background: #fff; }
.section-card.included { border-color: var(--brand-teal, #17B4A7); }
.section-header { padding: .65rem .75rem; cursor: pointer; user-select: none; display: flex; align-items: center; gap: .5rem; }
.section-body { padding: 0 .75rem .75rem; display: none; }
.section-card.expanded .section-body { display: block; }
.section-card.mandatory .section-toggle { pointer-events: none; }
.btn-view-section { line-height:1; padding:.15rem .4rem; font-size:.75rem; }
.section-card.expanded .btn-view-section .bi::before { content: "\f235"; } /* chevron-up */
.drag-handle { cursor: grab; color: #adb5bd; font-size: 1rem; flex-shrink: 0; }
.drag-handle:active { cursor: grabbing; }
.sortable-ghost { opacity: .4; background: #f0fdfa; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('engagement-letters.index') }}" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Letters</a>
        <h4 class="mb-0 mt-1">{{ $letter->exists ? 'Edit Letter' : 'New Engagement Letter' }}</h4>
    </div>
</div>

<form method="POST"
      action="{{ $letter->exists ? route('engagement-letters.update', $letter) : route('engagement-letters.store') }}"
      id="letterForm">
    @csrf
    @if($letter->exists) @method('PUT') @endif
    <input type="hidden" name="sections_json" id="sectionsJson">
    @if($renewal) <input type="hidden" name="renewal_id" value="{{ $renewal->id }}"> @endif

    {{-- Header fields --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Client <span class="text-danger">*</span></label>
                <select name="client_id" class="form-select" required id="clientSelect">
                    <option value="">— Select Client —</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}"
                            data-name="{{ $c->contact_name ?: $c->company_name }}"
                            data-company="{{ $c->company_name }}"
                                    data-email="{{ $c->email }}"
                            @selected(old('client_id', $letter->client_id ?? $renewal?->client_id) == $c->id)>
                            {{ $c->company_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold">Email Subject <span class="text-danger">*</span></label>
                <input type="text" name="subject" class="form-control" required
                       value="{{ old('subject', $letter->subject ?? 'Engagement Letter — ' . ($renewal?->client?->company_name ?? '')) }}"
                       placeholder="e.g. Engagement Letter — Acme Ltd">
            </div>
        </div>
    </div>

    {{-- Section builder --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
            <span>Letter Sections</span>
            <small class="text-muted fw-normal">Tick to include · drag to reorder · click <i class="bi bi-eye"></i> to view/edit wording</small>
        </div>
        <div class="card-body" id="sectionsList">
            @php
                $existingSections = collect($letter->sections ?? []);
                // Merge templates with any saved customisations
                $orderedTemplates = $templates;
                if ($existingSections->isNotEmpty()) {
                    $savedIds = $existingSections->pluck('template_id')->filter()->values();
                    $orderedTemplates = $templates->sortBy(function ($t) use ($savedIds) {
                        $pos = $savedIds->search($t->id);
                        return $pos === false ? 9999 : $pos;
                    })->values();
                }
            @endphp
            @foreach($orderedTemplates as $tpl)
                @php
                    $saved      = $existingSections->firstWhere('template_id', $tpl->id);
                    $isNew      = $existingSections->isEmpty();
                    $serviceMatch = $tpl->service_type && isset($clientServiceTypes) && $clientServiceTypes->contains($tpl->service_type);
                    $included   = $saved !== null || ($isNew && ($tpl->default_included || $tpl->is_mandatory || $serviceMatch));
                    $mandatory  = $tpl->is_mandatory;
                    $body       = $saved['body'] ?? $tpl->body;
                @endphp
                <div class="section-card {{ $included ? 'included' : '' }} {{ $mandatory ? 'mandatory' : '' }}"
                     data-template-id="{{ $tpl->id }}"
                     data-mandatory="{{ $mandatory ? '1' : '0' }}">
                    <div class="section-header">
                        <i class="bi bi-grip-vertical drag-handle"></i>
                        <div class="form-check mb-0 flex-shrink-0">
                            <input type="checkbox" class="form-check-input section-toggle"
                                   id="tpl_{{ $tpl->id }}"
                                   {{ $included ? 'checked' : '' }}
                                   {{ $mandatory ? 'disabled' : '' }}>
                        </div>
                        @if($mandatory)
                            <i class="bi bi-lock-fill text-warning flex-shrink-0" title="Mandatory section" style="font-size:.75rem;"></i>
                        @endif
                        <span class="fw-semibold flex-grow-1 mb-0 ms-1">{{ $tpl->title }}</span>
                        @if($tpl->service_type)
                            <span class="badge bg-light text-muted small me-1" style="font-size:.65rem;">{{ ucfirst($tpl->service_type) }}</span>
                        @endif
                        <button type="button" class="btn btn-outline-secondary btn-view-section" title="View / edit wording">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>
                    <div class="section-body">
                        <textarea class="form-control form-control-sm section-body-text" rows="5"
                                  placeholder="Section content...">{{ $body }}</textarea>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Actions --}}
    <div class="d-flex gap-2 align-items-center">
        <button type="button" id="draftBtn" class="btn btn-outline-secondary">
            <i class="bi bi-floppy me-1"></i>Save Draft
        </button>
        <button type="button" class="btn btn-primary" id="sendBtn">
            <i class="bi bi-send me-1"></i>Send to Client
        </button>
        <input type="hidden" name="action" id="actionInput" value="draft">
        <a href="{{ route('engagement-letters.index') }}" class="btn btn-link text-muted">Cancel</a>
    </div>
</form>

{{-- No-email modal --}}
<div class="modal fade" id="noEmailModal" tabindex="-1" aria-labelledby="noEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="noEmailModalLabel">
                    <i class="bi bi-envelope-exclamation me-2 text-warning"></i>No Email Address on Record
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">
                    <span id="noEmailClientName" class="fw-semibold"></span> has no email address saved.
                    Please add one to continue sending this letter.
                </p>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                    <input type="email" id="contactEmail" class="form-control" placeholder="client@example.com" required>
                    <div class="invalid-feedback">Please enter a valid email address.</div>
                </div>
                <div class="mb-1">
                    <label class="form-label fw-semibold">Phone <span class="text-muted fw-normal">(optional)</span></label>
                    <input type="text" id="contactPhone" class="form-control" placeholder="e.g. 01234 567890">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveContactBtn">
                    <i class="bi bi-check-lg me-1"></i>Save &amp; Send
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
(function () {
    var clientSelect  = document.getElementById('clientSelect');
    var subjectInput  = document.querySelector('input[name="subject"]');
    var letterForm    = document.getElementById('letterForm');
    var actionInput   = document.getElementById('actionInput');
    var noEmailModal  = new bootstrap.Modal(document.getElementById('noEmailModal'));
    var pendingClientId = null;

    // View button toggles wording textarea (independent of checkbox)
    document.querySelectorAll('.btn-view-section').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var card = this.closest('.section-card');
            card.classList.toggle('expanded');
            var icon = this.querySelector('i');
            icon.classList.toggle('bi-chevron-down', !card.classList.contains('expanded'));
            icon.classList.toggle('bi-chevron-up', card.classList.contains('expanded'));
            if (card.classList.contains('expanded')) {
                card.querySelector('.section-body-text').focus();
            }
        });
    });

    // Checkbox toggles included styling; mandatory sections are disabled (always on)
    document.querySelectorAll('.section-toggle:not([disabled])').forEach(function (cb) {
        cb.addEventListener('change', function () {
            this.closest('.section-card').classList.toggle('included', this.checked);
        });
    });

    // Sortable
    Sortable.create(document.getElementById('sectionsList'), {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
    });

    function buildSectionsJson() {
        var sections = [];
        document.querySelectorAll('#sectionsList .section-card').forEach(function (card) {
            var toggle = card.querySelector('.section-toggle');
            var isMandatory = card.dataset.mandatory === '1';
            if (toggle.checked || isMandatory) {
                sections.push({
                    template_id: parseInt(card.dataset.templateId),
                    title: card.querySelector('span.fw-semibold').textContent.trim(),
                    body: card.querySelector('.section-body-text').value,
                });
            }
        });
        return sections;
    }

    // Save Draft button
    document.getElementById('draftBtn').addEventListener('click', function () {
        var sections = buildSectionsJson();
        if (sections.length === 0) { alert('Please include at least one section.'); return; }
        document.getElementById('sectionsJson').value = JSON.stringify(sections);
        actionInput.value = 'draft';
        letterForm.submit();
    });

    // Send button: check email first
    document.getElementById('sendBtn').addEventListener('click', function () {
        var opt = clientSelect.options[clientSelect.selectedIndex];
        if (!opt || !opt.value) {
            clientSelect.focus();
            clientSelect.reportValidity();
            return;
        }

        var sections = buildSectionsJson();
        if (sections.length === 0) { alert('Please include at least one section.'); return; }
        document.getElementById('sectionsJson').value = JSON.stringify(sections);

        if (opt.dataset.email) {
            // Email on record — submit immediately
            actionInput.value = 'send';
            letterForm.submit();
        } else {
            // No email — show modal
            pendingClientId = opt.value;
            document.getElementById('noEmailClientName').textContent = opt.dataset.company;
            document.getElementById('contactEmail').value = '';
            document.getElementById('contactPhone').value = '';
            document.getElementById('contactEmail').classList.remove('is-invalid');
            noEmailModal.show();
            setTimeout(function () { document.getElementById('contactEmail').focus(); }, 300);
        }
    });

    // Modal Save & Send
    document.getElementById('saveContactBtn').addEventListener('click', function () {
        var emailInput = document.getElementById('contactEmail');
        var phoneInput = document.getElementById('contactPhone');
        var email = emailInput.value.trim();

        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            emailInput.classList.add('is-invalid');
            emailInput.focus();
            return;
        }
        emailInput.classList.remove('is-invalid');

        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

        fetch('/clients/' + pendingClientId + '/contact', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ email: email, phone: phoneInput.value.trim() || null }),
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                // Update the option's data-email so it's reflected if user comes back
                var opt = clientSelect.querySelector('option[value="' + pendingClientId + '"]');
                if (opt) opt.dataset.email = email;

                noEmailModal.hide();
                actionInput.value = 'send';
                letterForm.submit();
            } else {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Save &amp; Send';
                alert('Could not save contact details. Please try again.');
            }
        })
        .catch(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Save &amp; Send';
            alert('Network error. Please try again.');
        });
    });

    // Auto-update subject when client changes
    clientSelect.addEventListener('change', function () {
        var opt = this.options[this.selectedIndex];
        if (opt.value && !subjectInput.value.trim()) {
            subjectInput.value = 'Engagement Letter — ' + opt.dataset.company;
        } else if (opt.value && subjectInput.value.match(/^Engagement Letter — /)) {
            subjectInput.value = 'Engagement Letter — ' + opt.dataset.company;
        }
    });
})();
</script>
@endpush
