<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with('project.client');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('name', 'like', "%$s%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('urgent')) {
            $query->where('is_urgent', true);
        }

        $tasks    = $query->orderByRaw("is_urgent DESC")
            ->orderByRaw("CASE status WHEN 'in_progress' THEN 0 WHEN 'pending' THEN 1 ELSE 2 END")
            ->orderBy('due_date')
            ->paginate(25)
            ->withQueryString();

        $projects = Project::with('client')->orderBy('name')->pluck('name', 'id');

        return view('tasks.index', compact('tasks', 'projects'));
    }

    public function create(Request $request)
    {
        $projects = Project::with('client')->where('status', 'active')->orderBy('name')->get();
        $selected = $request->project_id;

        return view('tasks.create', compact('projects', 'selected'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id'  => 'required|exists:projects,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:pending,in_progress,completed,cancelled',
            'priority'    => 'required|in:low,medium,high',
            'due_date'    => 'nullable|date',
        ]);

        $data['is_urgent'] = $request->boolean('is_urgent');

        if ($data['status'] === 'completed') {
            $data['completed_at'] = now();
        }

        $task = Task::create($data);

        return redirect()->route('projects.show', $task->project_id)->with('success', 'Task created.');
    }

    public function show(Task $task)
    {
        $task->load('project.client');

        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $projects = Project::with('client')->where('status', 'active')->orderBy('name')->get();

        return view('tasks.edit', compact('task', 'projects'));
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'project_id'  => 'required|exists:projects,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:pending,in_progress,completed,cancelled',
            'priority'    => 'required|in:low,medium,high',
            'due_date'    => 'nullable|date',
        ]);

        $data['is_urgent'] = $request->boolean('is_urgent');

        if ($data['status'] === 'completed' && $task->status !== 'completed') {
            $data['completed_at'] = now();
        } elseif ($data['status'] !== 'completed') {
            $data['completed_at'] = null;
        }

        $task->update($data);

        return redirect()->route('tasks.index')->with('success', 'Task updated.');
    }

    public function toggleUrgent(Task $task)
    {
        $task->update(['is_urgent' => ! $task->is_urgent]);

        return response()->json([
            'is_urgent' => $task->is_urgent,
            'message'   => $task->is_urgent ? 'Task marked as urgent.' : 'Urgent flag removed.',
        ]);
    }

    public function destroy(Task $task)
    {
        abort_unless(auth()->user()->isManager(), 403);
        $projectId = $task->project_id;
        $task->delete();

        return redirect()->route('projects.show', $projectId)->with('success', 'Task deleted.');
    }
}
