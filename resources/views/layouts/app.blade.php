<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Focus') — Accounting Practice</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* ── Brand colour tokens ───────────────────────────────────────────── */
        :root {
            --brand-dark:   #0C3D38;
            --brand-teal:   #17B4A7;
            --brand-orange: #F7941D;
            --brand-blue:   #1A9DD9;
            /* Override Bootstrap primary */
            --bs-primary:              #17B4A7;
            --bs-primary-rgb:          23, 180, 167;
            --bs-link-color:           #17B4A7;
            --bs-link-hover-color:     #0ea397;
            --bs-link-color-rgb:       23, 180, 167;
        }

        /* ── Bootstrap component overrides ────────────────────────────────── */
        .btn-primary {
            --bs-btn-bg:                #17B4A7;
            --bs-btn-border-color:      #17B4A7;
            --bs-btn-hover-bg:          #0ea397;
            --bs-btn-hover-border-color:#0ea397;
            --bs-btn-active-bg:         #0c9088;
            --bs-btn-active-border-color:#0c9088;
            --bs-btn-focus-shadow-rgb:  23,180,167;
        }
        .btn-outline-primary {
            --bs-btn-color:             #17B4A7;
            --bs-btn-border-color:      #17B4A7;
            --bs-btn-hover-bg:          #17B4A7;
            --bs-btn-hover-border-color:#17B4A7;
            --bs-btn-active-bg:         #0ea397;
        }
        .bg-primary         { background-color: #17B4A7 !important; }
        .text-primary       { color: #17B4A7 !important; }
        .border-primary     { border-color: #17B4A7 !important; }
        .border-start.border-primary { border-color: #17B4A7 !important; }
        .bg-primary.bg-opacity-10 { background-color: rgba(23,180,167,.1) !important; }
        a { color: #17B4A7; }
        a:hover { color: #0ea397; }
        /* Keep white links white (nav, buttons etc.) */
        .nav-link, .btn, .dropdown-item, .alert a,
        .text-white a, .text-decoration-none { color: inherit; }
        .text-decoration-none:hover { color: inherit; }

        /* ── Sidebar ───────────────────────────────────────────────────────── */
        body { background-color: #f0f4f4; }
        .sidebar {
            min-height: 100vh;
            background: var(--brand-dark);
            width: 240px;
            flex-shrink: 0;
        }
        .sidebar .brand {
            display: flex;
            align-items: center;
            gap: .625rem;
            padding: 1.1rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar .brand img  { height: 34px; width: auto; }
        .sidebar .brand span { color: #fff; font-size: 1.2rem; font-weight: 700; letter-spacing: -.01em; }
        .sidebar .nav-link {
            color: rgba(255,255,255,.65);
            padding: .45rem 1.1rem;
            border-radius: .375rem;
            margin: .1rem .5rem;
            font-size: .875rem;
            transition: background .12s, color .12s;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(23,180,167,.18);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(23,180,167,.25);
            border-left: 3px solid var(--brand-teal);
            padding-left: calc(1.1rem - 3px);
        }
        .sidebar .nav-section {
            color: rgba(255,255,255,.35);
            font-size: .67rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            padding: .9rem 1.25rem .2rem;
        }

        /* ── Top bar ───────────────────────────────────────────────────────── */
        .main-content { flex: 1; overflow-x: hidden; }
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e3eceb;
            padding: .75rem 1.5rem;
        }
        .content-area { padding: 1.5rem; }

        /* ── Cards ─────────────────────────────────────────────────────────── */
        .stat-card { border: none; border-radius: .75rem; transition: transform .15s; }
        .stat-card:hover { transform: translateY(-2px); }
    </style>
    <style>
        @media print {
            .sidebar, .topbar, .report-actions, .no-print { display: none !important; }
            .d-flex { display: block !important; }
            .main-content { width: 100% !important; overflow: visible !important; }
            .content-area { padding: 0.5rem !important; }
            body { background: #fff !important; }
            .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; break-inside: avoid; }
            .card-header { background: #f8f9fa !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .table { font-size: 0.8rem; }
            a { color: inherit !important; text-decoration: none !important; }
            .badge { border: 1px solid #adb5bd; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="d-flex">
    <nav class="sidebar d-flex flex-column">
        <div class="brand">
            <img src="{{ asset('images/logo.png') }}" alt="Focus logo" onerror="this.style.display='none'">
            <span>Focus</span>
        </div>
        <div class="flex-grow-1 pt-2 d-flex flex-column">
            <div>
                <div class="nav-section">Overview</div>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>

                <div class="nav-section">Practice</div>
                <a href="{{ route('clients.index') }}" class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                    <i class="bi bi-people me-2"></i>Clients
                </a>
                <a href="{{ route('jobs.index') }}" class="nav-link {{ request()->routeIs('jobs.*') ? 'active' : '' }}">
                    <i class="bi bi-briefcase me-2"></i>Jobs
                </a>
                <a href="{{ route('projects.index') }}" class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                    <i class="bi bi-kanban me-2"></i>Projects
                </a>
                <a href="{{ route('tasks.index') }}" class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                    <i class="bi bi-check2-square me-2"></i>Tasks
                </a>

                <div class="nav-section">Services & Billing</div>
                <a href="{{ route('services.index') }}" class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}">
                    <i class="bi bi-grid me-2"></i>Services
                </a>
                <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam me-2"></i>Products
                </a>
                <a href="{{ route('renewals.index') }}" class="nav-link {{ request()->routeIs('renewals.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-repeat me-2"></i>Renewals
                </a>

                @can('manager')
                <div class="nav-section">Insights</div>
                <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line me-2"></i>Reports
                </a>

                <div class="nav-section">Admin</div>
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill me-2"></i>Users
                </a>
                <a href="{{ route('client-types.index') }}" class="nav-link {{ request()->routeIs('client-types.*') ? 'active' : '' }}">
                    <i class="bi bi-building me-2"></i>Client Types
                </a>
                <a href="{{ route('activity.index') }}" class="nav-link {{ request()->routeIs('activity.*') ? 'active' : '' }}">
                    <i class="bi bi-activity me-2"></i>Activity
                </a>
                @endcan
            </div>

            <div class="mt-auto pb-2">
                <div class="nav-section">My Details</div>
                <a href="{{ route('profile.password') }}" class="nav-link {{ request()->routeIs('profile.password') ? 'active' : '' }}">
                    <i class="bi bi-key me-2"></i>Password
                </a>
                <a href="{{ route('two-factor.index') }}" class="nav-link {{ request()->routeIs('two-factor.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock me-2"></i>Security
                </a>
            </div>
        </div>
    </nav>

    <div class="main-content d-flex flex-column">
        <div class="topbar d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-semibold text-muted">@yield('page-title', 'Dashboard')</h6>
            <div class="d-flex align-items-center gap-3">
                <div class="text-muted small">{{ now()->format('l, d F Y') }}</div>
                @auth
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small">
                        <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-box-arrow-right me-1"></i>Sign Out
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </div>

        @if(session()->has('impersonator_id'))
        <div class="d-flex align-items-center justify-content-between px-4 py-2 no-print"
             style="background:#F7941D;color:#fff;flex-shrink:0;">
            <span class="fw-semibold small">
                <i class="bi bi-eye-fill me-2"></i>Viewing as <strong>{{ Auth::user()->name }}</strong>
                <span class="fw-normal ms-2 opacity-75">— you are impersonating this account. Changes you make are real.</span>
            </span>
            <form method="POST" action="{{ route('impersonate.stop') }}" class="d-inline ms-3">
                @csrf
                <button type="submit" class="btn btn-sm fw-semibold"
                        style="background:#fff;color:var(--brand-dark);white-space:nowrap;">
                    <i class="bi bi-box-arrow-left me-1"></i>Return to {{ session('impersonator_name') }}
                </button>
            </form>
        </div>
        @endif

        <div class="content-area">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
