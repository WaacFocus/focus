<div class="card shadow-sm mb-4">
    <div class="card-header d-flex align-items-center justify-content-between py-2">
        <div>
            <span class="fw-semibold">{{ $groupLabel }}</span>
            <span class="text-muted small ms-2">{{ $groupSub }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button id="saveOrderBtn-{{ $groupKey }}" class="btn btn-sm btn-primary py-0" style="display:none;"
                    onclick="saveOrder('{{ $groupKey }}')">
                Save Order
            </button>
            <button class="btn btn-sm btn-outline-secondary"
                    data-bs-toggle="modal" data-bs-target="#statusModal"
                    onclick="openAddModal({{ $serviceId ?? 'null' }})">
                <i class="bi bi-plus-lg"></i> Add
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:2rem;"></th>
                    <th>Name</th>
                    <th style="width:8rem;">Color</th>
                    <th class="text-center" style="width:9rem;">Completion</th>
                    <th class="text-center" style="width:6rem;">Active</th>
                    <th style="width:7rem;"></th>
                </tr>
            </thead>
            <tbody data-sortable-group="{{ $groupKey }}">
                @forelse($statuses as $status)
                <tr data-id="{{ $status->id }}">
                    <td class="text-center text-muted drag-handle" style="cursor:grab;">
                        <i class="bi bi-grip-vertical"></i>
                    </td>
                    <td class="fw-semibold">{{ $status->name }}</td>
                    <td>
                        <span class="badge bg-{{ $status->color }}">{{ $status->color_label }}</span>
                    </td>
                    <td class="text-center">
                        @if($status->is_completion)
                            <i class="bi bi-check-circle-fill text-success" title="Completion status"></i>
                        @else
                            <i class="bi bi-dash-circle text-muted"></i>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($status->is_active)
                            <i class="bi bi-check-circle-fill text-success"></i>
                        @else
                            <i class="bi bi-dash-circle text-muted"></i>
                        @endif
                    </td>
                    <td class="text-end pe-3">
                        <button class="btn btn-sm btn-outline-secondary me-1"
                                onclick="openEditModal({{ $status->id }}, {{ json_encode($status->name) }}, {{ json_encode($status->color) }}, {{ json_encode($status->slug) }}, {{ $status->service_id ?? 'null' }}, {{ $status->is_completion ? 'true' : 'false' }}, {{ $status->is_active ? 'true' : 'false' }})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form method="POST" action="{{ route('admin.job-statuses.destroy', $status) }}" class="d-inline"
                              onsubmit="return confirm('Delete status &quot;{{ $status->name }}&quot;? Jobs with this status will keep it but it will not appear in dropdowns.')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-3 small">
                        No statuses defined — uses global statuses.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
