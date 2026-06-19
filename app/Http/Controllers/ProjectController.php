<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with('client')->withCount('tasks');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%$s%")
                ->orWhereHas('client', fn ($q2) => $q2->where('company_name', 'like', "%$s%")));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $projects = $query->latest()->paginate(20)->withQueryString();
        $clients  = Client::orderBy('company_name')->pluck('company_name', 'id');

        return view('projects.index', compact('projects', 'clients'));
    }

    public function create()
    {
        $clients  = Client::where('status', 'active')->orderBy('company_name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('projects.create', compact('clients', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'   => 'required|exists:clients,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:quote,active,on_hold,completed,cancelled',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'budget'      => 'nullable|numeric|min:0',
            'notes'       => 'nullable|string',
        ]);

        $project = Project::create($data);

        if ($request->filled('products')) {
            $sync = [];
            foreach ($request->products as $pid => $details) {
                if (!empty($details['quantity'])) {
                    $sync[$pid] = [
                        'quantity'   => $details['quantity'],
                        'unit_price' => $details['unit_price'],
                        'notes'      => $details['notes'] ?? null,
                    ];
                }
            }
            $project->products()->sync($sync);
        }

        return redirect()->route('projects.show', $project)->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $project->load(['client', 'tasks', 'products']);

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $clients  = Client::where('status', 'active')->orderBy('company_name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $project->load('products');

        return view('projects.edit', compact('project', 'clients', 'products'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'client_id'   => 'required|exists:clients,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:quote,active,on_hold,completed,cancelled',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'budget'      => 'nullable|numeric|min:0',
            'notes'       => 'nullable|string',
        ]);

        $project->update($data);

        $sync = [];
        if ($request->filled('products')) {
            foreach ($request->products as $pid => $details) {
                if (!empty($details['quantity'])) {
                    $sync[$pid] = [
                        'quantity'   => $details['quantity'],
                        'unit_price' => $details['unit_price'],
                        'notes'      => $details['notes'] ?? null,
                    ];
                }
            }
        }
        $project->products()->sync($sync);

        return redirect()->route('projects.show', $project)->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        abort_unless(auth()->user()->isManager(), 403);
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted.');
    }
}
