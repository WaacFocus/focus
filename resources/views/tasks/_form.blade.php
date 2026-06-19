<div class="card shadow-sm">
    <div class="card-body row g-3">
        <div class="col-12">
            <label class="form-label">Task Name <span class="text-danger">*</span></label>
            <input type="text" name="name" value="{{ old('name', $task->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            <label class="form-label">Project <span class="text-danger">*</span></label>
            <select name="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                <option value="">— Select Project —</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" @selected(old('project_id', ($task->project_id ?? $selected ?? null)) == $project->id)>
                        {{ $project->client->company_name }} — {{ $project->name }}
                    </option>
                @endforeach
            </select>
            @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" rows="3" class="form-control">{{ old('description', $task->description ?? '') }}</textarea>
        </div>
        <div class="col-md-4">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                @foreach(['pending' => 'Pending','in_progress' => 'In Progress','completed' => 'Completed','cancelled' => 'Cancelled'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('status', $task->status ?? 'pending') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Priority</label>
            <select name="priority" class="form-select">
                <option value="high" @selected(old('priority', $task->priority ?? 'medium') === 'high')>High</option>
                <option value="medium" @selected(old('priority', $task->priority ?? 'medium') === 'medium')>Medium</option>
                <option value="low" @selected(old('priority', $task->priority ?? 'medium') === 'low')>Low</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Due Date</label>
            <input type="date" name="due_date" value="{{ old('due_date', isset($task->due_date) ? $task->due_date->format('Y-m-d') : '') }}" class="form-control">
        </div>
        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_urgent" id="is_urgent" value="1"
                       @checked(old('is_urgent', $task->is_urgent ?? false))>
                <label class="form-check-label fw-semibold" for="is_urgent" style="color:#e85d04;">
                    <i class="bi bi-fire me-1"></i>Mark as Urgent
                </label>
            </div>
            <div class="form-text">Urgent tasks are highlighted and shown at the top of all task lists.</div>
        </div>
    </div>
</div>
