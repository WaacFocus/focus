<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('urgent')) {
            $query->where('is_urgent', true);
        }

        $tasks = $query->orderByRaw("is_urgent DESC")
            ->orderByRaw("CASE status WHEN 'in_progress' THEN 0 WHEN 'pending' THEN 1 ELSE 2 END")
            ->orderBy('due_date')
            ->paginate(25)
            ->withQueryString();

        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
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

        Task::create($data);

        return redirect()->route('tasks.index')->with('success', 'Task created.');
    }

    public function show(Task $task)
    {
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
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
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted.');
    }
}
