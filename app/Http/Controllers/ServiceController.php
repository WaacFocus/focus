<?php

namespace App\Http\Controllers;

use App\Models\EngagementLetterTemplate;
use App\Models\JobStatus;
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

        $perPage  = in_array((int) $request->input('per_page'), [25, 50, 100, 250]) ? (int) $request->input('per_page') : 25;
        $services = $query->orderBy('name')->paginate($perPage)->withQueryString();

        return view('services.index', compact('services'));
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $service = Service::create($data);

        // Seed service-specific job statuses by copying global defaults
        JobStatus::whereNull('service_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->each(function ($gs) use ($service) {
                JobStatus::create([
                    'service_id'    => $service->id,
                    'name'          => $gs->name,
                    'slug'          => $gs->slug,
                    'color'         => $gs->color,
                    'sort_order'    => $gs->sort_order,
                    'is_completion' => $gs->is_completion,
                    'is_active'     => true,
                ]);
            });

        // Link to or create an engagement letter section for this service
        $serviceType  = strtolower($service->name);
        $existingTpl  = EngagementLetterTemplate::whereRaw('LOWER(title) = ?', [$serviceType])->first();

        if ($existingTpl) {
            // Align the service_type so the builder matching works
            if ($existingTpl->service_type !== $serviceType) {
                $existingTpl->update(['service_type' => $serviceType]);
            }
        } else {
            EngagementLetterTemplate::create([
                'title'            => $service->name,
                'service_type'     => $serviceType,
                'body'             => "We are pleased to confirm the terms of our engagement to provide {$service->name} services.\n\nThe scope of our services will be agreed with you and confirmed in writing prior to commencement.",
                'sort_order'       => (EngagementLetterTemplate::max('sort_order') ?? 0) + 1,
                'is_active'        => true,
                'default_included' => false,
                'is_mandatory'     => false,
            ]);
        }

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
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $oldType = strtolower($service->name);
        $service->update($data);
        $newType = strtolower($service->name);

        // Keep the linked engagement letter template in sync with the service name
        if ($oldType !== $newType) {
            EngagementLetterTemplate::where('service_type', $oldType)
                ->update(['service_type' => $newType, 'title' => $service->name]);
        }

        return redirect()->route('services.index')->with('success', 'Service updated.');
    }

    public function destroy(Service $service)
    {
        abort_unless(auth()->user()->isManager(), 403);
        $service->delete();

        return redirect()->route('services.index')->with('success', 'Service deleted.');
    }
}
