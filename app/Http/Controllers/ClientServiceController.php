<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Job;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientServiceController extends Controller
{
    public function store(Request $request, Client $client)
    {
        $data = $request->validate([
            'service_id' => [
                'required',
                'exists:services,id',
                Rule::unique('client_service', 'service_id')->where('client_id', $client->id),
            ],
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'notes'           => 'nullable|string',
            'job_frequency'   => 'required|in:weekly,monthly,yearly,one-off',
            'job_assigned_to' => 'required|exists:users,id',
            'job_due_date'    => 'required|date',
        ], [
            'service_id.unique' => 'This service is already assigned to this client.',
        ]);

        $client->services()->attach($data['service_id'], [
            'start_date' => $data['start_date'] ?? null,
            'end_date'   => $data['end_date']   ?? null,
            'notes'      => $data['notes']      ?? null,
        ]);

        $service = Service::find($data['service_id']);

        Job::create([
            'name'        => $service->name,
            'description' => null,
            'client_id'   => $client->id,
            'assigned_to' => $data['job_assigned_to'],
            'frequency'   => $data['job_frequency'],
            'due_date'    => $data['job_due_date'],
            'status'      => 'pending',
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Service added and job created.']);
        }

        return back()->with('success', '"' . $service->name . '" added to client and job created.');
    }

    public function destroy(Request $request, Client $client, Service $service)
    {
        abort_unless(auth()->user()->isManager(), 403);

        $client->services()->detach($service->id);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Service removed.']);
        }

        return back()->with('success', '"' . $service->name . '" removed from client.');
    }
}
