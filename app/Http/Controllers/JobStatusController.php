<?php

namespace App\Http\Controllers;

use App\Models\JobStatus;
use App\Models\Service;
use Illuminate\Http\Request;

class JobStatusController extends Controller
{
    public function index()
    {
        $services = Service::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        $globalStatuses = JobStatus::whereNull('service_id')->orderBy('sort_order')->get();

        $serviceStatuses = JobStatus::whereNotNull('service_id')
            ->with('service')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('service_id');

        return view('admin.job-statuses.index', compact('services', 'globalStatuses', 'serviceStatuses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'slug'          => 'required|string|max:50|regex:/^[a-z0-9_]+$/',
            'color'         => 'required|string|in:secondary,in-progress,success,warning,danger,info,primary,dark',
            'service_id'    => 'nullable|exists:services,id',
            'is_completion' => 'boolean',
            'is_active'     => 'boolean',
        ]);

        // Uniqueness: slug must be unique within the same service group
        $existsQuery = JobStatus::where('slug', $data['slug']);
        if (! empty($data['service_id'])) {
            $existsQuery->where('service_id', $data['service_id']);
        } else {
            $existsQuery->whereNull('service_id');
        }
        if ($existsQuery->exists()) {
            return back()->withErrors(['slug' => 'A status with this slug already exists in this group.'])->withInput();
        }

        $data['is_completion'] = $request->boolean('is_completion');
        $data['is_active']     = $request->boolean('is_active', true);
        $data['sort_order']    = JobStatus::where('service_id', $data['service_id'] ?? null)->max('sort_order') + 1;

        JobStatus::create($data);

        return back()->with('success', 'Status "' . $data['name'] . '" created.');
    }

    public function update(Request $request, JobStatus $jobStatus)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'color'         => 'required|string|in:secondary,in-progress,success,warning,danger,info,primary,dark',
            'is_completion' => 'boolean',
            'is_active'     => 'boolean',
        ]);

        $data['is_completion'] = $request->boolean('is_completion');
        $data['is_active']     = $request->boolean('is_active', true);

        $jobStatus->update($data);

        return back()->with('success', 'Status updated.');
    }

    public function destroy(JobStatus $jobStatus)
    {
        $jobStatus->delete();
        return back()->with('success', 'Status deleted.');
    }

    public function reorder(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer|exists:job_statuses,id']);

        foreach ($request->ids as $order => $id) {
            JobStatus::where('id', $id)->update(['sort_order' => $order + 1]);
        }

        return response()->json(['message' => 'Order saved.']);
    }
}
