@extends('layouts.app')

@section('title', 'Engagement Letters')
@section('page-title', 'Engagement Letters')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Sent Letters</h4>
    <a href="{{ route('engagement-letters.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Letter</a>
</div>

@if(session('success'))
    <div class="alert alert-success py-2 small mb-3">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger py-2 small mb-3">{{ session('error') }}</div>
@endif

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Client</th>
                    <th>Subject</th>
                    <th>Created By</th>
                    <th class="text-center">Status</th>
                    <th>Sent</th>
                    <th>Signed</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($letters as $letter)
                <tr>
                    <td class="fw-medium">{{ $letter->client->company_name }}</td>
                    <td class="text-muted small">{{ $letter->subject }}</td>
                    <td class="small text-muted">{{ $letter->sentBy?->name ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge bg-{{ $letter->status_badge }}">{{ ucfirst($letter->status) }}</span>
                    </td>
                    <td class="text-muted small">{{ $letter->sent_at ? $letter->sent_at->format('d M Y') : '—' }}</td>
                    <td class="small">
                        @if($letter->signed_at)
                            {{ $letter->signed_at->format('d M Y') }}
                            <div class="text-muted" style="font-size:.7rem;">by {{ $letter->signed_name }}</div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-end" style="white-space:nowrap;">
                        <a href="{{ route('engagement-letters.show', $letter) }}"
                           class="btn btn-sm btn-outline-secondary" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if($letter->status !== 'signed')
                        <a href="{{ route('engagement-letters.edit', $letter) }}"
                           class="btn btn-sm btn-outline-secondary" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @endif
                        @if($letter->status === 'sent' || $letter->status === 'signed')
                        <form method="POST" action="{{ route('engagement-letters.send', $letter) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-outline-primary" title="Resend email to client"
                                    onclick="return confirm('Resend the engagement letter email to {{ addslashes($letter->client->company_name) }}?')">
                                <i class="bi bi-send"></i>
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('engagement-letters.pdf', $letter) }}"
                           class="btn btn-sm btn-outline-secondary" title="Download PDF" target="_blank">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </a>
                        @can('manager')
                        <form method="POST" action="{{ route('engagement-letters.destroy', $letter) }}" class="d-inline"
                              onsubmit="return confirm('Delete this letter?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No engagement letters yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white border-top">
        @include('partials.pagination', ['paginator' => $letters])
    </div>
</div>
@endsection
