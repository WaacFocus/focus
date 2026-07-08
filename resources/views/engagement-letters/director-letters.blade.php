@extends('layouts.app')

@section('title', 'Director Engagement Letters')
@section('page-title', 'Engagement Letters')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('engagement-letters.show', $letter) }}" class="text-muted text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>Back to letter
        </a>
        <h4 class="mb-0 mt-1">Director Engagement Letters</h4>
    </div>
    <a href="{{ route('engagement-letters.show', $letter) }}" class="btn btn-outline-secondary">
        <i class="bi bi-check-lg me-1"></i>Done
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div>{{ session('success') }}</div>
    </div>
@endif

<div class="alert alert-info d-flex align-items-start gap-2 mb-4">
    <i class="bi bi-info-circle-fill fs-5 flex-shrink-0 mt-1"></i>
    <div>
        <strong>{{ $company->company_name }}</strong> has directors who require a Self Assessment engagement letter.
        Each letter will include all mandatory sections plus the Self Assessment section.
        Send to each director below.
    </div>
</div>

@if($directorClients->isEmpty())
    <div class="card shadow-sm">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-people fs-2 mb-2 d-block"></i>
            No directors with Self Assessment required were found as active clients.
        </div>
    </div>
@else
    <div class="row g-3" id="directorCards">
        @foreach($directorClients as $dc)
            @php
                $dirClient  = $dc['client'];
                $director   = $dc['director'];
                $sentLetter = $dc['sent_letter'];
            @endphp
            <div class="col-md-6" id="director-card-{{ $dirClient->id }}">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1 fw-bold">{{ $director->name }}</h5>
                                <span class="text-muted small">{{ $director->getRoleLabel() }}</span>
                                @if($dirClient->client_code)
                                    <span class="badge bg-light text-secondary ms-2 small">{{ $dirClient->client_code }}</span>
                                @endif
                            </div>
                            @if($sentLetter)
                                <span class="badge text-white" style="background:#17B4A7;font-size:.8rem;padding:.4em .75em;">
                                    <i class="bi bi-check-circle me-1"></i>Sent
                                </span>
                            @endif
                        </div>

                        {{-- Email status --}}
                        <div class="mb-3">
                            @if($dirClient->email)
                                <span class="text-muted small"><i class="bi bi-envelope me-1"></i>{{ $dirClient->email }}</span>
                            @else
                                <span class="text-danger small"><i class="bi bi-envelope-exclamation me-1"></i>No email address on record</span>
                            @endif
                        </div>

                        {{-- Action --}}
                        @if($sentLetter)
                            <a href="{{ route('engagement-letters.show', $sentLetter) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye me-1"></i>View Letter
                            </a>
                        @else
                            <button type="button"
                                    class="btn btn-primary btn-send-director"
                                    data-client-id="{{ $dirClient->id }}"
                                    data-client-name="{{ $director->name }}"
                                    data-client-role="{{ $director->getRoleLabel() }}"
                                    data-email="{{ $dirClient->email }}"
                                    data-send-url="{{ route('engagement-letters.directors.send', [$letter, $dirClient]) }}"
                                    data-contact-url="{{ route('clients.contact.update', $dirClient) }}">
                                <i class="bi bi-send me-1"></i>Send Engagement Letter
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- No-email modal --}}
<div class="modal fade" id="directorEmailModal" tabindex="-1" aria-labelledby="directorEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="directorEmailModalLabel">
                    <i class="bi bi-envelope-exclamation me-2 text-warning"></i>No Email Address on Record
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-light border mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-person-fill fs-4 text-secondary"></i>
                    <div>
                        <div class="fw-bold fs-6" id="modalDirectorName"></div>
                        <div class="text-muted small" id="modalDirectorRole"></div>
                    </div>
                </div>
                <p class="text-muted mb-3">
                    Please add an email address to send the engagement letter to this director.
                </p>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                    <input type="email" id="directorEmail" class="form-control" placeholder="director@example.com" required>
                    <div class="invalid-feedback">Please enter a valid email address.</div>
                </div>
                <div class="mb-1">
                    <label class="form-label fw-semibold">Phone <span class="text-muted fw-normal">(optional)</span></label>
                    <input type="text" id="directorPhone" class="form-control" placeholder="e.g. 07700 900000">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveDirectorContactBtn">
                    <i class="bi bi-check-lg me-1"></i>Save &amp; Send
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var modal        = new bootstrap.Modal(document.getElementById('directorEmailModal'));
    var pendingBtn   = null;
    var csrfToken    = document.querySelector('meta[name="csrf-token"]').content;

    function setCardSent(clientId, letterId) {
        var card = document.getElementById('director-card-' + clientId);
        if (!card) return;
        var btn = card.querySelector('.btn-send-director');
        if (btn) {
            btn.outerHTML = '<span class="badge text-white" style="background:#17B4A7;font-size:.85rem;padding:.45em .8em;">'
                          + '<i class="bi bi-check-circle me-1"></i>Sent</span>';
        }
        var header = card.querySelector('.d-flex.justify-content-between');
        if (header && !header.querySelector('.badge')) {
            var badge = document.createElement('span');
            badge.className = 'badge text-white';
            badge.style.cssText = 'background:#17B4A7;font-size:.8rem;padding:.4em .75em;';
            badge.innerHTML = '<i class="bi bi-check-circle me-1"></i>Sent';
            header.appendChild(badge);
        }
    }

    function doSend(btn) {
        var origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Sending…';

        fetch(btn.dataset.sendUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                setCardSent(btn.dataset.clientId, data.letter_id);
            } else {
                btn.disabled = false;
                btn.innerHTML = origHtml;
                alert(data.error || 'Failed to send. Please try again.');
            }
        })
        .catch(function () {
            btn.disabled = false;
            btn.innerHTML = origHtml;
            alert('Network error. Please try again.');
        });
    }

    document.querySelectorAll('.btn-send-director').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (this.dataset.email) {
                doSend(this);
            } else {
                pendingBtn = this;
                document.getElementById('modalDirectorName').textContent = this.dataset.clientName;
                document.getElementById('modalDirectorRole').textContent  = this.dataset.clientRole;
                document.getElementById('directorEmail').value  = '';
                document.getElementById('directorPhone').value  = '';
                document.getElementById('directorEmail').classList.remove('is-invalid');
                modal.show();
                setTimeout(function () { document.getElementById('directorEmail').focus(); }, 300);
            }
        });
    });

    document.getElementById('saveDirectorContactBtn').addEventListener('click', function () {
        var emailInput = document.getElementById('directorEmail');
        var phoneInput = document.getElementById('directorPhone');
        var email      = emailInput.value.trim();

        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            emailInput.classList.add('is-invalid');
            emailInput.focus();
            return;
        }
        emailInput.classList.remove('is-invalid');

        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

        fetch(pendingBtn.dataset.contactUrl, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ email: email, phone: phoneInput.value.trim() || null }),
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                // Update the button's data-email so the email shows on card
                pendingBtn.dataset.email = email;

                // Update the email display on the card
                var card = document.getElementById('director-card-' + pendingBtn.dataset.clientId);
                var emailEl = card ? card.querySelector('.text-danger.small, .text-muted.small') : null;
                if (emailEl) {
                    emailEl.className = 'text-muted small';
                    emailEl.innerHTML = '<i class="bi bi-envelope me-1"></i>' + email;
                }

                modal.hide();
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Save &amp; Send';

                doSend(pendingBtn);
                pendingBtn = null;
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

    // Allow Enter key in email field to trigger save
    document.getElementById('directorEmail').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('saveDirectorContactBtn').click();
        }
    });
})();
</script>
@endpush
