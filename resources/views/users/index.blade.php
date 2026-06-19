@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'User Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Users</h4>
    <button class="btn btn-primary" type="button" onclick="openUserPanel()">
        <i class="bi bi-person-plus me-1"></i>New User
    </button>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th class="text-center">Role</th>
                    <th class="text-center">2FA</th>
                    <th class="text-muted small">Last Updated</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-semibold"
                                 style="width:34px;height:34px;background:var(--brand-teal,#17B4A7);font-size:.8rem;flex-shrink:0;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(strrchr($user->name, ' '), 1, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $user->name }}</div>
                                @if($user->id === auth()->id())
                                    <small class="text-muted">You</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td class="text-center">
                        @if($user->isManager())
                            <span class="badge bg-primary"><i class="bi bi-shield-check me-1"></i>Manager</span>
                        @else
                            <span class="badge bg-secondary"><i class="bi bi-person me-1"></i>User</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($user->hasTwoFactorEnabled())
                            <span class="badge rounded-pill text-bg-success" title="{{ $user->hasTotpEnabled() ? 'Authenticator' : '' }}{{ $user->hasTotpEnabled() && $user->hasPasskeys() ? ' + ' : '' }}{{ $user->hasPasskeys() ? 'Passkey' : '' }}">
                                <i class="bi bi-shield-check me-1"></i>On
                            </span>
                        @else
                            <span class="badge rounded-pill text-bg-light text-muted border">Off</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $user->updated_at->format('d M Y') }}</td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                onclick="openUserPanel({{ $user->id }})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('impersonate.start', $user) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-info" title="View site as {{ $user->name }}">
                                <i class="bi bi-eye"></i>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline"
                              onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                        @else
                        <button class="btn btn-sm btn-outline-info disabled" title="Cannot impersonate yourself"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-outline-danger disabled" title="Cannot delete own account"><i class="bi bi-trash"></i></button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@include('users._panel')
@endsection
