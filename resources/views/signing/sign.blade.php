<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Engagement Letter — {{ $letter->client->company_name }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">
    <link rel="shortcut icon" href="/favicon-32.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <style>
        body { background: #f0fafa; font-family: Arial, sans-serif; }
        .brand-header { background: #fff; border-bottom: 3px solid #3DBFB8; padding: 1rem 2rem; margin-bottom: 2rem; }
        .brand-header img { height: 42px; }
        .letter-meta { background: #3DBFB8; color: #fff; padding: 1.25rem 2rem; border-radius: .5rem .5rem 0 0; }
        .letter-meta .label { font-size: .7rem; letter-spacing: .08em; text-transform: uppercase; opacity: .75; }
        .letter-body { background: #fff; padding: 2rem; font-family: Georgia, serif; line-height: 1.85; font-size: 15px; border-radius: 0 0 .5rem .5rem; }
        .letter-section h3 { font-size: 14px; color: #3DBFB8; margin-top: 1.5rem; margin-bottom: .3rem; font-family: -apple-system, sans-serif; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
        .sign-box { background: #fff; border: 2px solid #3DBFB8; border-radius: .5rem; padding: 1.75rem; }
        .sign-box h5 { color: #3DBFB8; }
        .btn-sign { background: #3DBFB8; border: none; color: #fff; font-weight: 600; }
        .btn-sign:hover, .btn-sign:focus { background: #30a8a2; color: #fff; }
        .btn-mode { border: 2px solid #3DBFB8; color: #3DBFB8; background: #fff; font-weight: 600; transition: all .15s; }
        .btn-mode.active { background: #3DBFB8; color: #fff; }
        .btn-mode:hover:not(.active) { background: #f0fafa; }
        .sig-canvas-wrap { position: relative; }
        #sigCanvas, #typedCanvas {
            display: block; width: 100%; border: 2px dashed #3DBFB8;
            border-radius: .375rem; background: #fff; cursor: crosshair;
            touch-action: none;
        }
        #sigCanvas  { height: 165px; }
        #typedCanvas { height: 130px; cursor: default; }
        .canvas-hint {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            color: #b0c9c8; pointer-events: none; text-align: center; font-size: .9rem;
        }
        .ip-note { color: #888; font-size: .8rem; }
    </style>
</head>
<body>

<div class="brand-header d-flex align-items-center justify-content-between">
    <img src="{{ asset('images/woods-logo.png') }}" alt="Woods Accounting & Consulting">
    <span class="text-muted small">Engagement Letter</span>
</div>

<div class="container pb-5" style="max-width:780px;">

    <div class="shadow-sm overflow-hidden mb-4" style="border-radius:.5rem;">
        <div class="letter-meta">
            <div class="label">Engagement Letter</div>
            <h1 class="h4 mb-0 mt-1 fw-bold">{{ $letter->client->company_name }}</h1>
            <div class="mt-1" style="font-size:.85rem;opacity:.8;">Please read carefully and sign below</div>
        </div>
        <div class="letter-body">
            <p><strong>Dear {{ $letter->client->contact_first_name_greeting }},</strong></p>
            @foreach($letter->sections as $section)
            <div class="letter-section">
                <h3>{{ $section['title'] }}</h3>
                <p style="white-space:pre-line;margin-bottom:.5rem;">{{ $section['body'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger mb-4">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
    @endif

    <div class="sign-box mb-4">
        <h5 class="fw-semibold mb-1"><i class="bi bi-vector-pen me-2"></i>Sign this Engagement Letter</h5>
        <p class="text-muted small mb-4">Enter your full legal name, then draw or type your signature below.</p>

        <form method="POST" id="signForm" action="{{ route('sign.sign', $token) }}" novalidate>
            @csrf
            <input type="hidden" name="signature_data" id="sigData">
            <input type="hidden" name="signature_type" id="sigType" value="drawn">

            {{-- Name (always required) --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">Your Full Name <span class="text-danger">*</span></label>
                <input type="text" name="signed_name" id="fullName"
                       class="form-control form-control-lg @error('signed_name') is-invalid @enderror"
                       placeholder="Enter your full legal name"
                       value="{{ old('signed_name') }}"
                       required autocomplete="name">
                @error('signed_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Mode selector --}}
            <div class="d-flex gap-2 mb-3">
                <button type="button" id="btnDraw" class="btn btn-mode active flex-fill" onclick="setMode('drawn')">
                    <i class="bi bi-pen me-1"></i>Draw Signature
                </button>
                <button type="button" id="btnType" class="btn btn-mode flex-fill" onclick="setMode('typed')">
                    <i class="bi bi-fonts me-1"></i>Type Name
                </button>
            </div>

            {{-- Draw panel --}}
            <div id="drawPanel">
                <div class="sig-canvas-wrap mb-2">
                    <canvas id="sigCanvas" width="800" height="220"></canvas>
                    <div class="canvas-hint" id="drawHint">
                        <i class="bi bi-pen" style="font-size:1.8rem;display:block;margin-bottom:.3rem;"></i>
                        Sign here using your finger or mouse
                    </div>
                </div>
                <button type="button" id="clearBtn" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Clear
                </button>
            </div>

            {{-- Typed panel --}}
            <div id="typedPanel" style="display:none;">
                <p class="text-muted small mb-2">Your name will appear as your signature:</p>
                <canvas id="typedCanvas" width="800" height="160"></canvas>
            </div>

            {{-- Agreement --}}
            <div class="mb-4 mt-4">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input @error('agreed') is-invalid @enderror"
                           name="agreed" id="agreedCheck" value="1" required>
                    <label class="form-check-label" for="agreedCheck">
                        I confirm that I have read and understood the terms of this engagement letter and agree to be bound by them.
                    </label>
                    @error('agreed')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>

            <button type="submit" class="btn btn-sign btn-lg w-100" id="submitBtn">
                <i class="bi bi-check-circle me-2"></i>Sign Engagement Letter
            </button>
        </form>
    </div>

    <p class="text-center ip-note">
        Your IP address (<strong>{{ request()->ip() }}</strong>), browser, and the date and time of signing will be recorded.<br>
        A unique transaction ID will be generated and included in the signed PDF emailed to you.
    </p>

</div>

<script>
(function () {
    // ── Draw mode canvas ──────────────────────────────────────────────────────
    const sigCanvas = document.getElementById('sigCanvas');
    const sigCtx    = sigCanvas.getContext('2d');
    const drawHint  = document.getElementById('drawHint');
    let   isDrawing = false, lastX = 0, lastY = 0, hasStrokes = false;

    function initDraw() {
        sigCtx.fillStyle = '#fff';
        sigCtx.fillRect(0, 0, sigCanvas.width, sigCanvas.height);
        sigCtx.strokeStyle = '#1a1a2e';
        sigCtx.lineWidth   = 2.8;
        sigCtx.lineCap     = 'round';
        sigCtx.lineJoin    = 'round';
    }
    initDraw();

    function canvasXY(e) {
        const r = sigCanvas.getBoundingClientRect();
        const sx = sigCanvas.width  / r.width;
        const sy = sigCanvas.height / r.height;
        const src = e.touches ? e.touches[0] : e;
        return { x: (src.clientX - r.left) * sx, y: (src.clientY - r.top) * sy };
    }

    sigCanvas.addEventListener('mousedown',  function (e) { isDrawing = true; const p = canvasXY(e); lastX = p.x; lastY = p.y; });
    sigCanvas.addEventListener('touchstart', function (e) { e.preventDefault(); isDrawing = true; const p = canvasXY(e); lastX = p.x; lastY = p.y; }, { passive: false });

    function onMove(e) {
        if (!isDrawing) return;
        e.preventDefault();
        const p = canvasXY(e);
        sigCtx.beginPath();
        sigCtx.moveTo(lastX, lastY);
        sigCtx.lineTo(p.x, p.y);
        sigCtx.stroke();
        lastX = p.x; lastY = p.y;
        if (!hasStrokes) { hasStrokes = true; drawHint.style.display = 'none'; }
    }
    sigCanvas.addEventListener('mousemove',  onMove);
    sigCanvas.addEventListener('touchmove',  onMove, { passive: false });
    sigCanvas.addEventListener('mouseup',    function () { isDrawing = false; sigCtx.beginPath(); });
    sigCanvas.addEventListener('mouseleave', function () { isDrawing = false; sigCtx.beginPath(); });
    sigCanvas.addEventListener('touchend',   function () { isDrawing = false; sigCtx.beginPath(); });

    document.getElementById('clearBtn').addEventListener('click', function () {
        initDraw();
        hasStrokes = false;
        drawHint.style.display = '';
    });

    // ── Typed mode canvas ─────────────────────────────────────────────────────
    const typedCanvas = document.getElementById('typedCanvas');
    const typedCtx    = typedCanvas.getContext('2d');

    function renderTyped() {
        const name = document.getElementById('fullName').value.trim();
        typedCtx.clearRect(0, 0, typedCanvas.width, typedCanvas.height);
        typedCtx.fillStyle = '#fff';
        typedCtx.fillRect(0, 0, typedCanvas.width, typedCanvas.height);

        // Baseline
        typedCtx.strokeStyle = '#c8eeec';
        typedCtx.lineWidth   = 1.5;
        typedCtx.beginPath();
        typedCtx.moveTo(typedCanvas.width * 0.05, typedCanvas.height * 0.72);
        typedCtx.lineTo(typedCanvas.width * 0.95, typedCanvas.height * 0.72);
        typedCtx.stroke();

        if (!name) return;
        const fontSize = Math.min(62, Math.max(28, Math.floor(typedCanvas.width / (name.length * 0.52))));
        typedCtx.font          = fontSize + 'px "Dancing Script", "Zapf Chancery", cursive';
        typedCtx.fillStyle     = '#1a1a2e';
        typedCtx.textAlign     = 'center';
        typedCtx.textBaseline  = 'alphabetic';
        typedCtx.fillText(name, typedCanvas.width / 2, typedCanvas.height * 0.68);
    }

    document.fonts.ready.then(renderTyped);
    document.getElementById('fullName').addEventListener('input', function () {
        if (document.getElementById('sigType').value === 'typed') renderTyped();
    });

    // ── Mode switching ────────────────────────────────────────────────────────
    window.setMode = function (mode) {
        document.getElementById('sigType').value = mode;
        document.getElementById('drawPanel').style.display  = mode === 'drawn' ? '' : 'none';
        document.getElementById('typedPanel').style.display = mode === 'typed'  ? '' : 'none';
        document.getElementById('btnDraw').classList.toggle('active', mode === 'drawn');
        document.getElementById('btnType').classList.toggle('active', mode === 'typed');
        if (mode === 'typed') renderTyped();
    };

    // ── Form submit ───────────────────────────────────────────────────────────
    document.getElementById('signForm').addEventListener('submit', function (e) {
        const mode = document.getElementById('sigType').value;

        if (mode === 'drawn') {
            if (!hasStrokes) {
                e.preventDefault();
                alert('Please draw your signature before submitting.');
                return;
            }
            document.getElementById('sigData').value = sigCanvas.toDataURL('image/png');
        } else {
            const name = document.getElementById('fullName').value.trim();
            if (!name) {
                e.preventDefault();
                document.getElementById('fullName').focus();
                return;
            }
            renderTyped(); // ensure latest name is rendered
            document.getElementById('sigData').value = typedCanvas.toDataURL('image/png');
        }

        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Signing…';
    });
})();
</script>
</body>
</html>
