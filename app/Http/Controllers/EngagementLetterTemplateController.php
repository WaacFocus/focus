<?php

namespace App\Http\Controllers;

use App\Models\EngagementLetterTemplate;
use Illuminate\Http\Request;

class EngagementLetterTemplateController extends Controller
{
    public function index()
    {
        $templates = EngagementLetterTemplate::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.engagement-letter-templates.index', compact('templates'));
    }

    public function create()
    {
        $template = new EngagementLetterTemplate();
        return view('admin.engagement-letter-templates.form', compact('template'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'service_type' => 'nullable|string|max:100',
            'body'         => 'required|string',
            'sort_order'   => 'nullable|integer|min:0',
            'is_active'    => 'nullable|boolean',
        ]);

        $data['is_active']        = $request->boolean('is_active', true);
        $data['default_included'] = $request->boolean('default_included');
        $data['is_mandatory']     = $request->boolean('is_mandatory');
        $data['sort_order']       = $data['sort_order'] ?? (EngagementLetterTemplate::max('sort_order') + 1);

        EngagementLetterTemplate::create($data);

        return redirect()->route('admin.engagement-letter-templates.index')
            ->with('success', 'Section added.');
    }

    public function edit(EngagementLetterTemplate $engagementLetterTemplate)
    {
        $template = $engagementLetterTemplate;
        return view('admin.engagement-letter-templates.form', compact('template'));
    }

    public function update(Request $request, EngagementLetterTemplate $engagementLetterTemplate)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'service_type' => 'nullable|string|max:100',
            'body'         => 'required|string',
            'sort_order'   => 'nullable|integer|min:0',
            'is_active'    => 'nullable|boolean',
        ]);

        $data['is_active']        = $request->boolean('is_active');
        $data['default_included'] = $request->boolean('default_included');
        $data['is_mandatory']     = $request->boolean('is_mandatory');

        $engagementLetterTemplate->update($data);

        return redirect()->route('admin.engagement-letter-templates.index')
            ->with('success', 'Section updated.');
    }

    public function reorder(Request $request)
    {
        $request->validate(['order' => 'required|array']);
        foreach ($request->order as $pos => $id) {
            EngagementLetterTemplate::where('id', $id)->update(['sort_order' => $pos + 1]);
        }
        return response()->json(['ok' => true]);
    }

    public function destroy(EngagementLetterTemplate $engagementLetterTemplate)
    {
        $engagementLetterTemplate->delete();
        return redirect()->route('admin.engagement-letter-templates.index')
            ->with('success', 'Section deleted.');
    }
}
