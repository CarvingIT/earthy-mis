<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index()
    {
        $leads = Lead::latest()->get();
        return view('leads.index', compact('leads'));
    }

    public function create()
    {
        return view('leads.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'society_name'   => ['required', 'string', 'max:255'],
            'contact_name'   => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:255'],
            'details'        => ['nullable', 'string'],
        ]);

        Lead::create($data);

        return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
    }

    public function edit(Lead $lead)
    {
        return view('leads.edit', compact('lead'));
    }

    public function update(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'society_name'   => ['required', 'string', 'max:255'],
            'contact_name'   => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:255'],
            'details'        => ['nullable', 'string'],
        ]);

        $lead->update($data);

        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }
}
