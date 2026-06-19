<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::withCount('clients');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $services = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('services.index', compact('services'));
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'default_price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,quarterly,annually,one_off',
            'is_active'     => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Service::create($data);

        return redirect()->route('services.index')->with('success', 'Service created.');
    }

    public function show(Service $service)
    {
        $service->load('clients');

        return view('services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'default_price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,quarterly,annually,one_off',
            'is_active'     => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $service->update($data);

        return redirect()->route('services.index')->with('success', 'Service updated.');
    }

    public function destroy(Service $service)
    {
        abort_unless(auth()->user()->isManager(), 403);
        $service->delete();

        return redirect()->route('services.index')->with('success', 'Service deleted.');
    }
}
