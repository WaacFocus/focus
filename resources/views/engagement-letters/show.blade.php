@extends('layouts.app')

@section('title', 'Engagement Letter')
@section('page-title', 'Engagement Letters')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('engagement-letters.index') }}" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Letters</a>
        <h4 class="mb-0 mt-1">{{ $letter->subject }}</h4>
    </div>
    <div class="d-flex gap-2">
        @if($letter->status === 'draft')
            <a href="{{ route('engagement-letters.edit', $letter) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Edit</a>
            <form method="POST" action="{{ route('engagement-letters.send', $letter) }}">
                @csrf
                <button class="btn btn-primary"><i class="bi bi-send me-1"></i>Send to Client</button>
            </form>
        @elseif($letter->status === 'sent')
            <a href="{{ route('engagement-letters.edit', $letter) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Edit</a>
            <form method="POST" action="{{ route('engagement-letters.send', $letter) }}">
                @csrf
                <button class="btn btn-outline-primary"><i class="bi bi-arrow-clockwise me-1"></i>Resend</button>
            </form>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Details</div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Client:</small>
                    <a href="{{ route('clients.show', $letter->client) }}" class="ms-1">{{ $letter->client->company_name }}</a>
                </div>
                @if($letter->renewal)
                <div class="mb-2"><small class="text-muted">Linked to:</small> <span class="ms-1">{{ $letter->renewal->description }}</span></div>
                @endif
                <div class="mb-2">
                    <small class="text-muted">Status:</small>
                    <span class="badge bg-{{ $letter->status_badge }} ms-1">{{ ucfirst($letter->status) }}</span>
                </div>
                @if($letter->sent_at)
                <div class="mb-2"><small class="text-muted">Sent:</small> <span class="ms-1">{{ $letter->sent_at->format('d M Y, H:i') }}</span></div>
                @endif
                @if($letter->sentBy)
                <div class="mb-2"><small class="text-muted">Sent by:</small> <span class="ms-1">{{ $letter->sentBy->name }}</span></div>
                @endif
                @if($letter->signed_at)
                <hr class="my-2">
                <div class="mb-1"><small class="text-muted">Signed by:</small> <strong class="ms-1">{{ $letter->signed_name }}</strong></div>
                <div class="mb-1"><small class="text-muted">Signed:</small> <span class="ms-1">{{ $letter->signed_at->format('d M Y, H:i') }}</span></div>
                <div class="mb-1"><small class="text-muted">IP Address:</small> <code class="ms-1">{{ $letter->signed_ip }}</code></div>
                <div class="mb-1"><small class="text-muted">Method:</small> <span class="ms-1">{{ $letter->signature_type === 'drawn' ? 'Hand-drawn' : ($letter->signature_type === 'typed' ? 'Typed' : '—') }}</span></div>
                @if($letter->transaction_id)
                <div class="mb-1"><small class="text-muted">Transaction ID:</small> <code class="ms-1" style="font-size:.7rem;">{{ $letter->transaction_id }}</code></div>
                @endif
                @if($letter->signature_image)
                <div class="mt-2 p-2 rounded" style="background:#f8f9fa;border:1px solid #dee2e6;">
                    <small class="text-muted d-block mb-1">Signature:</small>
                    <img src="{{ $letter->signature_image }}" alt="Signature" style="max-height:50px;max-width:220px;">
                </div>
                @endif
                @endif
            </div>
        </div>

        @if($letter->status === 'sent' && $letter->token)
        <div class="card shadow-sm border-warning mb-4">
            <div class="card-body">
                <div class="small fw-semibold mb-1"><i class="bi bi-link-45deg me-1"></i>Signing Link</div>
                <div class="text-break small text-muted mb-2" id="signingLinkText">{{ route('sign.show', $letter->token) }}</div>
                <button class="btn btn-sm btn-outline-secondary w-100" id="copySigningLink"
                        data-url="{{ route('sign.show', $letter->token) }}">
                    <i class="bi bi-clipboard me-1"></i>Copy Link
                </button>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Letter Content</div>
            <div class="card-body" style="font-family: Georgia, serif; line-height: 1.7;">
                @foreach($letter->sections as $section)
                    <h3 style="font-size:15px;color:#0C3D38;margin-top:1.5rem;margin-bottom:.4rem;">{{ $section['title'] }}</h3>
                    <p style="margin:0 0 .5rem;white-space:pre-line;font-size:14px;">{{ $section['body'] }}</p>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var btn = document.getElementById('copySigningLink');
    if (!btn) return;

    btn.addEventListener('click', function () {
        var url  = this.dataset.url;
        var self = this;

        function markCopied() {
            self.innerHTML = '<i class="bi bi-check-lg me-1"></i>Copied!';
            self.classList.add('btn-success');
            self.classList.remove('btn-outline-secondary');
            setTimeout(function () {
                self.innerHTML = '<i class="bi bi-clipboard me-1"></i>Copy Link';
                self.classList.remove('btn-success');
                self.classList.add('btn-outline-secondary');
            }, 2000);
        }

        function fallback() {
            var el = document.createElement('textarea');
            el.value = url;
            el.style.cssText = 'position:fixed;left:-9999px;top:-9999px;opacity:0;';
            document.body.appendChild(el);
            el.focus();
            el.select();
            try { document.execCommand('copy'); markCopied(); } catch (e) { prompt('Copy this link:', url); }
            document.body.removeChild(el);
        }

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(url).then(markCopied).catch(fallback);
        } else {
            fallback();
        }
    });
})();
</script>
@endpush
