<?php

namespace App\Http\Controllers;

use App\Models\ClientType;
use Illuminate\Http\Request;

class ClientTypeController extends Controller
{
    public function __construct()
    {
        abort_unless(auth()->check() && auth()->user()->isManager(), 403);
    }

    public function index()
    {
        $clientTypes = ClientType::withCount('clients')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('client-types.index', compact('clientTypes'));
    }

    public function show(ClientType $clientType)
    {
        return response()->json($clientType);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100|unique:client_types,name',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active']  = true;

        $type = ClientType::create($data);

        return response()->json(['message' => 'Client type created.', 'id' => $type->id]);
    }

    public function update(Request $request, ClientType $clientType)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100|unique:client_types,name,' . $clientType->id,
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active']  = $request->boolean('is_active');

        $clientType->update($data);

        return response()->json(['message' => 'Client type updated.']);
    }

    public function destroy(ClientType $clientType)
    {
        $count = $clientType->clients()->count();

        if ($count > 0) {
            return response()->json([
                'message' => "Cannot delete — this type is assigned to {$count} client(s). Reassign them first.",
            ], 422);
        }

        $clientType->delete();

        return response()->json(['message' => 'Client type deleted.']);
    }
}
